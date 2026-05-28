<x-filament-panels::page>
    @vite(['resources/js/app.js'])

    <div
        x-data="laravelVideoChat()"
        x-init="init()"
        @video-chat:start.window="startCall()"
        @video-chat:leave.window="leaveCall()"
        class="space-y-6">

        <x-filament::section>
            <x-slot name="heading">
                {{ __('Participants') }}
            </x-slot>

            <div class="video-chat-grid">
                <div class="video-chat-tile bg-black shadow-sm ring-1 ring-gray-950/10 dark:ring-white/10">
                    <video
                        x-ref="localVideo"
                        data-local-video
                        autoplay
                        playsinline
                        muted
                        class="h-full w-full scale-x-[-1] object-cover video-chat-tile">
                    </video>
                </div>

                <div class="video-chat-tile bg-black shadow-sm ring-1 ring-gray-950/10 dark:ring-white/10">
                    <video
                        x-ref="remoteVideo"
                        data-remote-video
                        autoplay
                        playsinline
                        class="h-full w-full object-cover">
                    </video>
                </div>

                <div class="video-chat-tile bg-black shadow-sm ring-1 ring-gray-950/10 dark:ring-white/10">
                    <video
                        autoplay
                        playsinline
                        class="h-full w-full object-cover">
                    </video>
                </div>
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
                status: 'Inicjalizacja...',
                connectionState: 'idle',
                isInCall: false,
                echoReady: false,
                remoteStreamActive: false,
                audioEnabled: true,
                videoEnabled: true,
                localStream: null,
                peerConnection: null,
                channel: null,
                roomId: 'pokoj-glowny',
                roomLabel: 'Pokój rozmowy',

                rtcConfig: {
                    iceServers: [{
                            urls: 'stun:stun.l.google.com:19302'
                        },
                        {
                            urls: 'stun:stun1.l.google.com:19302'
                        }
                    ]
                },

                async init() {
                    window.PartyMakerVideoChat = this;

                    Alpine.store('videoChat', {
                        isInCall: false
                    });

                    this.roomId = this.resolveRoomId();
                    this.roomLabel = this.roomId.startsWith('event-') ? `Pokój eventu ${this.roomId.replace('event-', '')}` : 'Pokój rozmowy';

                    let attempts = 0;
                    const checkEcho = setInterval(() => {
                        if (window.Echo) {
                            clearInterval(checkEcho);
                            this.echoReady = true;
                            this.status = 'Gotowy do połączenia';
                        } else {
                            attempts++;
                            if (attempts > 50) {
                                clearInterval(checkEcho);
                                this.status = 'Nie udało się załadować systemu rozmów';
                            }
                        }
                    }, 100);
                },

                resolveRoomId() {
                    const match = window.location.pathname.match(/\/event\/([^/]+)\/video-chat/);

                    if (match && match[1]) {
                        return `event-${match[1]}`;
                    }

                    return this.roomId;
                },

                get callInfo() {
                    if (!this.echoReady) return 'Ładowanie połączenia z serwerem';
                    if (!this.isInCall) return 'Kamera i mikrofon uruchomią się po dołączeniu';
                    if (this.remoteStreamActive) return 'Połączenie aktywne';

                    return 'Szukam drugiej osoby w pokoju';
                },

                syncCallState() {
                    if (Alpine.store('videoChat')) {
                        Alpine.store('videoChat').isInCall = this.isInCall;
                    }
                },

                getLocalVideo() {
                    return this.$refs.localVideo ?? this.$root.querySelector('[data-local-video]');
                },

                getRemoteVideo() {
                    return this.$refs.remoteVideo ?? this.$root.querySelector('[data-remote-video]');
                },

                async startCall() {
                    try {
                        this.localStream = await navigator.mediaDevices.getUserMedia({
                            video: true,
                            audio: true
                        });
                        const localVideo = this.getLocalVideo();

                        if (localVideo) {
                            localVideo.srcObject = this.localStream;
                        }
                    } catch (e) {
                        this.status = 'Brak dostępu do kamery lub mikrofonu';
                        return;
                    }

                    this.isInCall = true;
                    this.syncCallState();
                    this.connectionState = 'connecting';
                    this.status = 'Łączenie z pokojem...';
                    this.connectToSignalingServer();
                },

                connectToSignalingServer() {
                    this.channel = window.Echo.join(`chat.${this.roomId}`)
                        .here((users) => {
                            this.status = `W pokoju: ${users.length} ${users.length === 1 ? 'osoba' : 'osoby'}`;
                            this.connectionState = users.length > 1 ? 'connected' : 'connecting';
                            if (users.length > 1) {
                                this.createPeerConnection();
                                this.createOffer();
                            }
                        })
                        .joining((user) => {
                            this.status = 'Ktoś dołączył do rozmowy';
                            this.connectionState = 'connected';
                            if (!this.peerConnection) {
                                this.createPeerConnection();
                            }
                        })
                        .leaving((user) => {
                            this.status = 'Rozmówca rozłączył się';
                            this.connectionState = 'connecting';
                            this.remoteStreamActive = false;
                        })
                        .listenForWhisper('signal', (data) => {
                            this.handleSignal(data);
                        });
                },

                createPeerConnection() {
                    if (this.peerConnection) return;

                    this.peerConnection = new RTCPeerConnection(this.rtcConfig);

                    this.localStream.getTracks().forEach(track => {
                        this.peerConnection.addTrack(track, this.localStream);
                    });

                    this.peerConnection.ontrack = (event) => {
                        const remoteVideo = this.getRemoteVideo();

                        if (remoteVideo) {
                            remoteVideo.srcObject = event.streams[0];
                        }
                        this.remoteStreamActive = true;
                        this.connectionState = 'connected';
                        this.status = 'Połączenie aktywne';
                    };

                    this.peerConnection.onicecandidate = (event) => {
                        if (event.candidate) {
                            this.sendSignal({
                                type: 'candidate',
                                candidate: event.candidate
                            });
                        }
                    };
                },

                async createOffer() {
                    const offer = await this.peerConnection.createOffer();
                    await this.peerConnection.setLocalDescription(offer);
                    this.sendSignal({
                        type: 'offer',
                        sdp: offer
                    });
                },

                async createAnswer() {
                    const answer = await this.peerConnection.createAnswer();
                    await this.peerConnection.setLocalDescription(answer);
                    this.sendSignal({
                        type: 'answer',
                        sdp: answer
                    });
                },

                async handleSignal(data) {
                    if (!this.peerConnection) this.createPeerConnection();

                    try {
                        if (data.type === 'offer') {
                            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
                            await this.createAnswer();
                        } else if (data.type === 'answer') {
                            await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
                        } else if (data.type === 'candidate') {
                            await this.peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
                        }
                    } catch (error) {
                        console.error('Błąd sygnału:', error);
                        this.status = 'Wystąpił błąd połączenia';
                    }
                },

                sendSignal(data) {
                    if (this.channel) {
                        this.channel.whisper('signal', data);
                    }
                },

                toggleAudio() {
                    if (!this.localStream) return;

                    this.audioEnabled = !this.audioEnabled;
                    this.localStream.getAudioTracks().forEach(track => {
                        track.enabled = this.audioEnabled;
                    });
                },

                toggleVideo() {
                    if (!this.localStream) return;

                    this.videoEnabled = !this.videoEnabled;
                    this.localStream.getVideoTracks().forEach(track => {
                        track.enabled = this.videoEnabled;
                    });
                },

                leaveCall() {
                    if (this.channel) {
                        window.Echo.leave(`chat.${this.roomId}`);
                    }

                    if (this.peerConnection) {
                        this.peerConnection.close();
                    }

                    if (this.localStream) {
                        this.localStream.getTracks().forEach(track => track.stop());
                    }

                    this.channel = null;
                    this.peerConnection = null;
                    this.localStream = null;
                    this.isInCall = false;
                    this.syncCallState();
                    this.remoteStreamActive = false;
                    this.connectionState = 'idle';
                    this.audioEnabled = true;
                    this.videoEnabled = true;
                    this.status = 'Rozmowa zakończona';

                    const localVideo = this.getLocalVideo();
                    const remoteVideo = this.getRemoteVideo();

                    if (localVideo) {
                        localVideo.srcObject = null;
                    }

                    if (remoteVideo) {
                        remoteVideo.srcObject = null;
                    }
                }
            };
        }
    </script>
</x-filament-panels::page>
