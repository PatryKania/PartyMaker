<?php

use App\Events\WebRtcSignal;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('video chat signal endpoint requires authentication', function () {
    $this->postJson('/video-chat/signal', [
        'channel_name' => 'presence-video.1',
        'data' => ['type' => 'offer'],
    ])->assertUnauthorized();
});

test('authenticated user can send a webrtc signal', function () {
    Event::fake([WebRtcSignal::class]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/video-chat/signal', [
            'channel_name' => 'presence-video.1',
            'data' => [
                'type' => 'offer',
                'sdp' => 'fake-sdp',
            ],
        ])
        ->assertOk()
        ->assertJson(['status' => 'ok']);

    Event::assertDispatched(WebRtcSignal::class, function (WebRtcSignal $event) {
        return $event->channelName === 'presence-video.1'
            && $event->data['type'] === 'offer'
            && $event->broadcastAs() === 'signal';
    });
});

