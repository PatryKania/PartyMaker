<x-filament-panels::page>
    @vite(['resources/js/app.js'])

    <div
        x-data="laravelVideoChat()"
        x-init="init()"
        class="grid grid-cols-1 gap-6">

        <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow dark:bg-gray-800">
            <div>
                <div class="text-sm text-gray-500">
                    Status: <span x-text="status" class="font-medium text-primary-600"></span>
                </div>
            </div>

            <div x-show="!isInCall">
                <button
                    @click="startCall"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-500 text-white rounded shadow transition disabled:opacity-50"
                    :disabled="!echoReady">
                    <span x-show="echoReady">Dołącz do rozmowy</span>
                    <span x-show="!echoReady">Ładowanie systemu...</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="relative bg-black rounded-xl overflow-hidden aspect-video shadow-lg ring-1 ring-gray-900/10">
                <video x-ref="localVideo" autoplay playsinline muted class="w-full h-full object-cover transform scale-x-[-1]"></video>
            </div>

            <div class="relative bg-black rounded-xl overflow-hidden aspect-video shadow-lg ring-1 ring-gray-900/10">
                <video x-ref="remoteVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                <div x-show="!isInCall || !remoteStreamActive" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 bg-gray-900/90 z-10">
                    <x-heroicon-o-users class="w-12 h-12 mb-2 opacity-50" />
                    <span>Oczekiwanie na połączenie...</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        function laravelVideoChat() {
            return {
                status: 'Inicjalizacja...',
                isInCall: false,
                echoReady: false,
                remoteStreamActive: false,
                localStream: null,
                peerConnection: null,
                channel: null,
                roomId: 'pokoj-glowny',

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
                    let attempts = 0;
                    const checkEcho = setInterval(() => {
                        if (window.Echo) {
                            clearInterval(checkEcho);
                            this.echoReady = true;
                            this.status = 'Gotowy. Kliknij "Dołącz".';
                        } else {
                            attempts++;
                            if (attempts > 50) {
                                clearInterval(checkEcho);
                            }
                        }
                    }, 100);
                },

                async startCall() {
                    try {
                        this.localStream = await navigator.mediaDevices.getUserMedia({
                            video: true,
                            audio: true
                        });
                        this.$refs.localVideo.srcObject = this.localStream;
                    } catch (e) {
                        this.status = 'Brak dostępu do kamery';
                        return;
                    }

                    this.isInCall = true;
                    this.status = 'Łączenie z pokojem...';
                    this.connectToSignalingServer();
                },

                connectToSignalingServer() {
                    this.channel = window.Echo.join(`chat.${this.roomId}`)
                        .here((users) => {
                            this.status = `W pokoju: ${users.length} osób`;
                            console.log('Obecni:', users);
                            if (users.length > 1) {
                                this.createPeerConnection();
                                this.createOffer();
                            }
                        })
                        .joining((user) => {
                            this.status = 'Ktoś dołączył...';
                            if (!this.peerConnection) {
                                this.createPeerConnection();
                            }
                        })
                        .leaving((user) => {
                            this.status = 'Rozmówca rozłączył się';
                            this.remoteStreamActive = false;
                        })
                        .listenForWhisper('signal', (data) => {
                            console.log("📨 Sygnał:", data.type);
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
                        this.$refs.remoteVideo.srcObject = event.streams[0];
                        this.remoteStreamActive = true;
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
                        console.error("Błąd sygnału:", error);
                    }
                },

                sendSignal(data) {
                    if (this.channel) {
                        this.channel.whisper('signal', data);
                    }
                }
            };
        }
    </script>
</x-filament-panels::page>