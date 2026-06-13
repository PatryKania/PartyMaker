<?php

use App\Enums\ParticipantRole;
use App\Enums\ParticipantStatus;
use App\Filament\Actions\ConfirmInvitationAction;
use App\Filament\Actions\RejectInvitationAction;
use App\Filament\Actions\SendInvitationAction;
use App\Filament\Actions\SendInvitationsBulkAction;
use App\Mail\ParticipantInvitation;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

test('organizer can send a single invitation to a new participant', function () {
    Mail::fake();

    [$event, $organizer] = createInvitationOrganizer();
    $participant = Participant::factory()->create([
        'event_id' => $event->id,
        'email' => 'guest@example.com',
        'status' => ParticipantStatus::New,
    ]);

    $this->actingAs($organizer);

    SendInvitationAction::make()
        ->record($participant)
        ->call();

    expect($participant->refresh()->status)->toBe(ParticipantStatus::Pending);

    Mail::assertQueued(ParticipantInvitation::class, function (ParticipantInvitation $mail) use ($participant) {
        return $mail->participant->is($participant)
            && $mail->hasTo('guest@example.com');
    });
});

test('single invitation is not sent when participant has no email', function () {
    Mail::fake();

    [$event, $organizer] = createInvitationOrganizer();
    $participant = Participant::factory()->create([
        'event_id' => $event->id,
        'email' => null,
        'status' => ParticipantStatus::New,
    ]);

    $this->actingAs($organizer);

    SendInvitationAction::make()
        ->record($participant)
        ->call();

    expect($participant->refresh()->status)->toBe(ParticipantStatus::New);

    Mail::assertNothingQueued();
});

test('bulk invitation sends only new participants with email', function () {
    Mail::fake();

    [$event, $organizer] = createInvitationOrganizer();
    $sendable = Participant::factory()->create([
        'event_id' => $event->id,
        'email' => 'sendable@example.com',
        'status' => ParticipantStatus::New,
    ]);
    $alreadyPending = Participant::factory()->create([
        'event_id' => $event->id,
        'email' => 'pending@example.com',
        'status' => ParticipantStatus::Pending,
    ]);
    $withoutEmail = Participant::factory()->create([
        'event_id' => $event->id,
        'email' => null,
        'status' => ParticipantStatus::New,
    ]);

    $this->actingAs($organizer);

    SendInvitationsBulkAction::make()
        ->livewire(new class extends Component {
            public function render(): string
            {
                return '';
            }

            public function deselectAllTableRecords(): void
            {
            }
        })
        ->call(['records' => new Collection([$sendable, $alreadyPending, $withoutEmail])]);

    expect($sendable->refresh()->status)->toBe(ParticipantStatus::Pending)
        ->and($alreadyPending->refresh()->status)->toBe(ParticipantStatus::Pending)
        ->and($withoutEmail->refresh()->status)->toBe(ParticipantStatus::New);

    Mail::assertQueued(ParticipantInvitation::class, 1);
    Mail::assertQueued(ParticipantInvitation::class, function (ParticipantInvitation $mail) use ($sendable) {
        return $mail->participant->is($sendable)
            && $mail->hasTo('sendable@example.com');
    });
});

test('related participant can confirm a pending invitation', function () {
    [$event, $user] = createInvitationUser();
    $participant = Participant::factory()
        ->forUser($user)
        ->create([
            'event_id' => $event->id,
            'status' => ParticipantStatus::Pending,
        ]);

    $this->actingAs($user);

    expect(ConfirmInvitationAction::make()->record($participant)->isVisible())->toBeTrue();

    ConfirmInvitationAction::make()
        ->record($participant)
        ->call();

    expect($participant->refresh()->status)->toBe(ParticipantStatus::Confirmed);
});

test('related participant can reject a pending or confirmed invitation', function (ParticipantStatus $initialStatus) {
    [$event, $user] = createInvitationUser();
    $participant = Participant::factory()
        ->forUser($user)
        ->create([
            'event_id' => $event->id,
            'status' => $initialStatus,
        ]);

    $this->actingAs($user);

    expect(RejectInvitationAction::make()->record($participant)->isVisible())->toBeTrue();

    RejectInvitationAction::make()
        ->record($participant)
        ->call();

    expect($participant->refresh()->status)->toBe(ParticipantStatus::Rejected);
})->with([
    'pending invitation' => ParticipantStatus::Pending,
    'confirmed invitation' => ParticipantStatus::Confirmed,
]);

function createInvitationOrganizer(): array
{
    [$event, $organizer] = createInvitationUser();

    Participant::factory()
        ->forUser($organizer)
        ->organizer()
        ->create(['event_id' => $event->id]);

    return [$event, $organizer];
}

function createInvitationUser(): array
{
    $event = Event::factory()->create();
    Invitation::factory()->create(['event_id' => $event->id]);

    return [$event, User::factory()->create()];
}
