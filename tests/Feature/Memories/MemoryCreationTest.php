<?php

use App\Enums\ParticipantRole;
use App\Enums\ParticipantStatus;
use App\Filament\EventPanel\Resources\Memories\Pages\ManageMemories;
use App\Models\Event;
use App\Models\Memory;
use App\Models\Participant;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

afterEach(function () {
    Filament::setTenant(null, isQuiet: true);
    Filament::setCurrentPanel(null);
});

test('memory page creates memory record and related image records from uploaded paths', function () {
    Storage::fake('public');

    $event = Event::factory()->create();
    $user = User::factory()->create();
    Participant::factory()
        ->forUser($user)
        ->create([
            'event_id' => $event->id,
            'role' => ParticipantRole::Guest,
            'status' => ParticipantStatus::Confirmed,
        ]);

    Storage::disk('public')->put('memories/images/photo.jpg', 'fake-image');
    Filament::setCurrentPanel('event');
    Filament::setTenant($event, isQuiet: true);

    Livewire::actingAs($user)
        ->test(ManageMemories::class)
        ->set('data.desc', 'Great party memory')
        ->set('data.images', ['memories/images/photo.jpg'])
        ->call('create')
        ->assertHasNoErrors();

    $memory = Memory::query()->where('desc', 'Great party memory')->firstOrFail();

    expect($memory->event_id)->toBe($event->id)
        ->and($memory->user_id)->toBe($user->id)
        ->and($memory->memoryMedia()->where('type', 'image')->where('path', 'memories/images/photo.jpg')->exists())->toBeTrue()
        ->and($memory->memoryMedia()->where('type', 'video')->exists())->toBeFalse();
});

test('memory video upload currently has inconsistent form state handling', function () {
    Storage::fake('public');

    $event = Event::factory()->create();
    $user = User::factory()->create();
    Participant::factory()
        ->forUser($user)
        ->confirmed()
        ->create(['event_id' => $event->id]);

    Storage::disk('public')->put('memories/videos/video.mp4', 'fake-video');

    Filament::setCurrentPanel('event');
    Filament::setTenant($event, isQuiet: true);

    expect(fn () => Livewire::actingAs($user)
        ->test(ManageMemories::class)
        ->set('data.desc', 'Video memory')
        ->set('data.video', 'memories/videos/video.mp4')
        ->call('create'))
        ->toThrow(TypeError::class);
});
