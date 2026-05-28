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
                userId: @js(auth()->id()),
                isInCall: false,
                localStream: null,
                channel: null,
                roomId: 'pokoj-glowny',
                peerConnections: {},
                remoteStreams: [],

                rtcConfig: {
                    iceServers: [
                        { urls: 'stun:stun.l.google.com:19302' },
                        { urls: 'stun:stun1.l.google.com:19302' },
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
                                .filter((user) => user.id !== this.userId)
                                .forEach((user) => this.createOffer(user.id));
                        })
                        .joining((user) => {
                            if (user.id !== this.userId) {
                                this.createPeerConnection(user.id);
                            }
                        })
                        .leaving((user) => {
                            this.removeParticipant(user.id);
                        })
                        .listenForWhisper('signal', (data) => {
                            this.handleSignal(data);
                        });
                },

                createPeerConnection(userId) {
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
                    const existingStream = this.remoteStreams.find((participant) => participant.userId === userId);

                    if (existingStream) {
                        existingStream.stream = stream;
                        return;
                    }

                    this.remoteStreams.push({ userId, stream });
                },

                removeParticipant(userId) {
                    this.peerConnections[userId]?.close();
                    delete this.peerConnections[userId];
                    this.remoteStreams = this.remoteStreams.filter((participant) => participant.userId !== userId);
                },

                async createOffer(userId) {
                    const peerConnection = this.createPeerConnection(userId);
                    const offer = await peerConnection.createOffer();

                    await peerConnection.setLocalDescription(offer);

                    this.sendSignal(userId, {
                        type: 'offer',
                        sdp: offer,
                    });
                },

                async createAnswer(userId) {
                    const peerConnection = this.createPeerConnection(userId);
                    const answer = await peerConnection.createAnswer();

                    await peerConnection.setLocalDescription(answer);

                    this.sendSignal(userId, {
                        type: 'answer',
                        sdp: answer,
                    });
                },

                async handleSignal(data) {
                    if (data.to !== this.userId || data.from === this.userId) return;

                    const peerConnection = this.createPeerConnection(data.from);

                    try {
                        if (data.type === 'offer') {
                            await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
                            await this.createAnswer(data.from);
                        }

                        if (data.type === 'answer') {
                            await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
                        }

                        if (data.type === 'candidate') {
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
                        to,
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
                    this.remoteStreams = [];
                    this.localStream = null;
                    this.isInCall = false;
                    this.syncCallState();
                },
            };
        }
    </script>
</x-filament-panels::page>