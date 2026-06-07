<?php

use App\Enums\ParticipantRole;
use App\Enums\ParticipantStatus;
use App\Models\Event;
use App\Models\Gift;
use App\Models\Invitation;
use App\Models\Memory;
use App\Models\Participant;
use App\Models\Schedule;
use App\Models\Survey;
use App\Models\Task;
use App\Models\User;
use App\Policies\GiftPolicy;
use App\Policies\InvitationPolicy;
use App\Policies\MemoryPolicy;
use App\Policies\ParticipantPolicy;
use App\Policies\SchedulePolicy;
use App\Policies\SurveyPolicy;
use App\Policies\TaskPolicy;
use Filament\Facades\Filament;

afterEach(function () {
    Filament::setTenant(null, isQuiet: true);
});

test('organizer can manage event resources', function () {
    [$event, $organizer] = createPolicyUserForEvent(
        role: ParticipantRole::Organizer,
        status: ParticipantStatus::Confirmed,
    );

    Filament::setTenant($event, isQuiet: true);

    assertPolicyMatrix($organizer, createPolicyRecords($event, $organizer), [
        GiftPolicy::class => ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => true],
        InvitationPolicy::class => ['viewAny' => true, 'view' => true, 'create' => true, 'update' => true, 'delete' => true, 'deleteAny' => true, 'restore' => true, 'forceDelete' => true],
        MemoryPolicy::class => ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => true],
        ParticipantPolicy::class => ['viewAny' => true, 'view' => true, 'create' => true, 'update' => true, 'delete' => true],
        SchedulePolicy::class => ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => true],
        SurveyPolicy::class => ['viewAny' => true, 'view' => true, 'create' => true, 'update' => true, 'delete' => true, 'deleteAny' => true, 'restore' => true, 'forceDelete' => true],
        TaskPolicy::class => ['viewAny' => true, 'create' => true, 'update' => true, 'delete' => true],
    ]);
});

test('confirmed guest can only use participant level resources', function () {
    [$event, $guest] = createPolicyUserForEvent(
        role: ParticipantRole::Guest,
        status: ParticipantStatus::Confirmed,
    );

    Filament::setTenant($event, isQuiet: true);

    assertPolicyMatrix($guest, createPolicyRecords($event, $guest), [
        GiftPolicy::class => ['viewAny' => true, 'create' => false, 'update' => false, 'delete' => false],
        InvitationPolicy::class => ['viewAny' => false, 'view' => false, 'create' => false, 'update' => false, 'delete' => false, 'deleteAny' => false, 'restore' => false, 'forceDelete' => false],
        MemoryPolicy::class => ['viewAny' => true, 'create' => true, 'update' => false, 'delete' => false],
        ParticipantPolicy::class => ['viewAny' => true, 'view' => true, 'create' => false, 'update' => false, 'delete' => false],
        SchedulePolicy::class => ['viewAny' => true, 'create' => false, 'update' => false, 'delete' => false],
        SurveyPolicy::class => ['viewAny' => true, 'view' => false, 'create' => false, 'update' => false, 'delete' => false, 'deleteAny' => false, 'restore' => false, 'forceDelete' => false],
        TaskPolicy::class => ['viewAny' => false, 'create' => false, 'update' => false, 'delete' => false],
    ]);
});

test('unconfirmed guest cannot use protected event resources', function (ParticipantStatus $status) {
    [$event, $guest] = createPolicyUserForEvent(
        role: ParticipantRole::Guest,
        status: $status,
    );

    Filament::setTenant($event, isQuiet: true);

    assertPolicyMatrix($guest, createPolicyRecords($event, $guest), [
        GiftPolicy::class => ['viewAny' => false, 'create' => false],
        MemoryPolicy::class => ['viewAny' => false, 'create' => false],
        ParticipantPolicy::class => ['viewAny' => false, 'view' => false, 'create' => false],
        SchedulePolicy::class => ['viewAny' => false, 'create' => false],
        SurveyPolicy::class => ['viewAny' => false, 'view' => false, 'create' => false],
        TaskPolicy::class => ['viewAny' => false, 'create' => false],
        InvitationPolicy::class => ['viewAny' => false, 'create' => false],
    ]);
})->with([
    'new participant' => ParticipantStatus::New,
    'pending participant' => ParticipantStatus::Pending,
    'rejected participant' => ParticipantStatus::Rejected,
]);

test('user outside an event cannot use event resource policies', function () {
    $event = Event::factory()->create();
    $user = User::factory()->create();

    Filament::setTenant($event, isQuiet: true);

    assertPolicyMatrix($user, createPolicyRecords($event, $user), [
        GiftPolicy::class => ['viewAny' => false, 'create' => false],
        MemoryPolicy::class => ['viewAny' => false, 'create' => false],
        ParticipantPolicy::class => ['viewAny' => false, 'view' => false, 'create' => false],
        SchedulePolicy::class => ['viewAny' => false, 'create' => false],
        SurveyPolicy::class => ['viewAny' => false, 'view' => false, 'create' => false],
        TaskPolicy::class => ['viewAny' => false, 'create' => false],
        InvitationPolicy::class => ['viewAny' => false, 'create' => false],
    ]);
});

function createPolicyUserForEvent(ParticipantRole $role, ParticipantStatus $status): array
{
    $event = Event::factory()->create();
    $user = User::factory()->create();

    Participant::factory()
        ->forUser($user)
        ->create([
            'event_id' => $event->id,
            'role' => $role,
            'status' => $status,
        ]);

    return [$event, $user];
}

function createPolicyRecords(Event $event, User $user): array
{
    return [
        GiftPolicy::class => Gift::factory()->create(['event_id' => $event->id]),
        InvitationPolicy::class => Invitation::factory()->create(['event_id' => $event->id]),
        MemoryPolicy::class => Memory::factory()->create([
            'event_id' => $event->id,
            'user_id' => $user->id,
        ]),
        ParticipantPolicy::class => Participant::factory()->create(['event_id' => $event->id]),
        SchedulePolicy::class => Schedule::factory()->create(['event_id' => $event->id]),
        SurveyPolicy::class => Survey::factory()->create(['event_id' => $event->id]),
        TaskPolicy::class => Task::factory()->create(['event_id' => $event->id]),
    ];
}

function assertPolicyMatrix(User $user, array $records, array $expectations): void
{
    foreach ($expectations as $policyClass => $actions) {
        $policy = app($policyClass);

        foreach ($actions as $action => $expected) {
            $arguments = match ($action) {
                'viewAny', 'create', 'deleteAny' => [$user],
                default => [$user, $records[$policyClass]],
            };

            test()->assertSame(
                $expected,
                $policy->{$action}(...$arguments),
                "{$policyClass}::{$action} returned an unexpected result.",
            );
        }
    }
}
