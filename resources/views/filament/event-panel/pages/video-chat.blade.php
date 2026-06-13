<x-filament-panels::page>
    @vite(['resources/js/app.js'])

    <div x-data="laravelVideoChat()" x-init="init()" class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                {{ __('Participants') }}
            </x-slot>

            <div class="video-chat-grid">
                <template x-if="isInCall">
                    <div class="video-chat-tile bg-black shadow-sm ring-1 ring-gray-950/10 dark:ring-white/10">
                        <video
                            x-ref="localVideo"
                            data-local-video
                            autoplay
                            playsinline
                            muted
                            class="h-full w-full scale-x-[-1] object-cover">
                        </video>
                    </div>
                </template>

                <template x-for="participant in remoteStreams" :key="participant.userId">
                    <div class="video-chat-tile bg-black shadow-sm ring-1 ring-gray-950/10 dark:ring-white/10">
                        <video
                            autoplay
                            playsinline
                            class="h-full w-full object-cover"
                            x-init="$el.srcObject = participant.stream"
                            x-effect="$el.srcObject = participant.stream">
                        </video>
                    </div>
                </template>
            </div>
        </x-filament::section>
    </div>

    <style>
        .video-chat-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .video-chat-tile {
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 1rem;
        }

        .video-chat-tile video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        @media (min-width: 768px) {
            .video-chat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 1024px) {
            .video-chat-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>

    <script>
        function laravelVideoChat() {
            return {
                userId: String(@js(auth()->id())),
                isInCall: false,
                localStream: null,
                channel: null,
                roomId: 'pokoj-glowny',
                peerConnections: {},
                pendingIceCandidates: {},
                remoteStreams: [],

                rtcConfig: {
                    iceServers: [
                        { urls: 'stun:stun.l.google.com:19302' },
                        { urls: 'stun:stun1.l.google.com:19302' },
                        ...(@js(config('services.turn.url')) ? [{
                            urls: @js(config('services.turn.url')),
                            username: @js(config('services.turn.username')),
                            credential: @js(config('services.turn.credential')),
                        }] : []),
                    ],
                },

                init() {
                    window.PartyMakerVideoChat = this;

                    Alpine.store('videoChat', {
                        isInCall: false,
                    });

                    this.roomId = this.resolveRoomId();
                },

                resolveRoomId() {
                    const match = window.location.pathname.match(/\/event\/([^/]+)\/video-chat/);

                    return match?.[1] ? `event-${match[1]}` : this.roomId;
                },

                syncCallState() {
                    Alpine.store('videoChat').isInCall = this.isInCall;
                },

                normalizeUserId(userId) {
                    return String(userId);
                },

                shouldCreateOffer(userId) {
                    return this.userId.localeCompare(this.normalizeUserId(userId), undefined, { numeric: true }) < 0;
                },

                getLocalVideo() {
                    return this.$refs.localVideo ?? this.$root.querySelector('[data-local-video]');
                },

                clearVideo(video) {
                    if (!video) return;

                    video.pause();
                    video.srcObject = null;
                    video.removeAttribute('src');
                    video.load();
                },

                waitForEcho() {
                    return new Promise((resolve) => {
                        let attempts = 0;

                        const interval = setInterval(() => {
                            attempts++;

                            if (window.Echo || attempts > 50) {
                                clearInterval(interval);
                                resolve(Boolean(window.Echo));
                            }
                        }, 100);
                    });
                },

                async startCall() {
                    if (this.isInCall || !(await this.waitForEcho())) return;

                    try {
                        this.localStream = await navigator.mediaDevices.getUserMedia({
                            video: true,
                            audio: true,
                        });
                    } catch (error) {
                        return;
                    }

                    this.isInCall = true;
                    this.syncCallState();

                    this.$nextTick(() => {
                        const localVideo = this.getLocalVideo();

                        if (localVideo) {
                            localVideo.srcObject = this.localStream;
                        }
                    });

                    this.connectToSignalingServer();
                },

                connectToSignalingServer() {
                    this.channel = window.Echo.join(`chat.${this.roomId}`)
                        .here((users) => {
                            users
                                .map((user) => ({ ...user, id: this.normalizeUserId(user.id) }))
                                .filter((user) => user.id !== this.userId)
                                .forEach((user) => {
                                    this.createPeerConnection(user.id);

                                    if (this.shouldCreateOffer(user.id)) {
                                        this.createOffer(user.id);
                                    }
                                });
                        })
                        .joining((user) => {
                            const userId = this.normalizeUserId(user.id);

                            if (userId !== this.userId) {
                                this.createPeerConnection(userId);

                                if (this.shouldCreateOffer(userId)) {
                                    this.createOffer(userId);
                                }
                            }
                        })
                        .leaving((user) => {
                            this.removeParticipant(this.normalizeUserId(user.id));
                        })
                        .listenForWhisper('signal', (data) => {
                            this.handleSignal(data);
                        });
                },

                createPeerConnection(userId) {
                    userId = this.normalizeUserId(userId);

                    if (this.peerConnections[userId]) {
                        return this.peerConnections[userId];
                    }

                    const peerConnection = new RTCPeerConnection(this.rtcConfig);

                    this.localStream.getTracks().forEach((track) => {
                        peerConnection.addTrack(track, this.localStream);
                    });

                    peerConnection.ontrack = (event) => {
                        this.addRemoteStream(userId, event.streams[0]);
                    };

                    peerConnection.onicecandidate = (event) => {
                        if (event.candidate) {
                            this.sendSignal(userId, {
                                type: 'candidate',
                                candidate: event.candidate,
                            });
                        }
                    };

                    this.peerConnections[userId] = peerConnection;

                    return peerConnection;
                },

                addRemoteStream(userId, stream) {
                    userId = this.normalizeUserId(userId);

                    const existingStream = this.remoteStreams.find((participant) => participant.userId === userId);

                    if (existingStream) {
                        this.remoteStreams = this.remoteStreams.map((participant) => {
                            if (participant.userId !== userId) {
                                return participant;
                            }

                            return { ...participant, stream };
                        });

                        return;
                    }

                    this.remoteStreams.push({ userId, stream });
                },

                removeParticipant(userId) {
                    userId = this.normalizeUserId(userId);

                    this.peerConnections[userId]?.close();
                    delete this.peerConnections[userId];
                    delete this.pendingIceCandidates[userId];
                    this.remoteStreams = this.remoteStreams.filter((participant) => participant.userId !== userId);
                },

                async createOffer(userId) {
                    userId = this.normalizeUserId(userId);

                    const peerConnection = this.createPeerConnection(userId);
                    const offer = await peerConnection.createOffer();

                    await peerConnection.setLocalDescription(offer);

                    this.sendSignal(userId, {
                        type: 'offer',
                        sdp: offer,
                    });
                },

                async createAnswer(userId) {
                    userId = this.normalizeUserId(userId);

                    const peerConnection = this.createPeerConnection(userId);
                    const answer = await peerConnection.createAnswer();

                    await peerConnection.setLocalDescription(answer);

                    this.sendSignal(userId, {
                        type: 'answer',
                        sdp: answer,
                    });
                },

                async flushPendingIceCandidates(userId, peerConnection) {
                    userId = this.normalizeUserId(userId);

                    const candidates = this.pendingIceCandidates[userId] ?? [];
                    delete this.pendingIceCandidates[userId];

                    for (const candidate of candidates) {
                        await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
                    }
                },

                async handleSignal(data) {
                    const from = this.normalizeUserId(data.from);
                    const to = this.normalizeUserId(data.to);

                    if (to !== this.userId || from === this.userId) return;

                    const peerConnection = this.createPeerConnection(from);

                    try {
                        if (data.type === 'offer') {
                            await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
                            await this.flushPendingIceCandidates(from, peerConnection);
                            await this.createAnswer(from);
                        }

                        if (data.type === 'answer') {
                            await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
                            await this.flushPendingIceCandidates(from, peerConnection);
                        }

                        if (data.type === 'candidate') {
                            if (!peerConnection.remoteDescription) {
                                this.pendingIceCandidates[from] ??= [];
                                this.pendingIceCandidates[from].push(data.candidate);

                                return;
                            }

                            await peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
                        }
                    } catch (error) {
                        console.error(error);
                    }
                },

                sendSignal(to, payload) {
                    this.channel?.whisper('signal', {
                        ...payload,
                        from: this.userId,
                        to: this.normalizeUserId(to),
                    });
                },

                leaveCall() {
                    if (this.channel) {
                        window.Echo.leave(`chat.${this.roomId}`);
                    }

                    Object.values(this.peerConnections).forEach((peerConnection) => peerConnection.close());
                    this.localStream?.getTracks().forEach((track) => track.stop());

                    this.clearVideo(this.getLocalVideo());
                    this.channel = null;
                    this.peerConnections = {};
                    this.pendingIceCandidates = {};
                    this.remoteStreams = [];
                    this.localStream = null;
                    this.isInCall = false;
                    this.syncCallState();
                },
            };
        }
    </script>
</x-filament-panels::page>
