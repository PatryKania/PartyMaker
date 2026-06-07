<?php

use App\Enums\ParticipantRole;
use App\Enums\ParticipantStatus;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Exceptions\HttpResponseException;

afterEach(function () {
    Filament::setTenant(null, isQuiet: true);
});

function attachUserToEvent(
    User $user,
    Event $event,
    ParticipantRole $role = ParticipantRole::Guest,
    ParticipantStatus $status = ParticipantStatus::Confirmed,
): Participant {
    return Participant::factory()
        ->forUser($user)
        ->create([
            'event_id' => $event->id,
            'role' => $role,
            'status' => $status,
        ]);
}

test('organizer can access an event tenant and has organizer permissions', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    attachUserToEvent(
        user: $user,
        event: $event,
        role: ParticipantRole::Organizer,
        status: ParticipantStatus::Confirmed,
    );

    Filament::setTenant($event, isQuiet: true);

    expect($user->canAccessTenant($event))->toBeTrue()
        ->and($user->isOrganizer())->toBeTrue()
        ->and($user->hasPermissions())->toBeTrue();
});

test('confirmed guest can access an event tenant and use participant features', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    attachUserToEvent(
        user: $user,
        event: $event,
        status: ParticipantStatus::Confirmed,
    );

    Filament::setTenant($event, isQuiet: true);

    expect($user->canAccessTenant($event))->toBeTrue()
        ->and($user->isOrganizer())->toBeFalse()
        ->and($user->hasPermissions())->toBeTrue();
});

test('pending guest can enter an event tenant but does not have confirmed permissions', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    attachUserToEvent(
        user: $user,
        event: $event,
        status: ParticipantStatus::Pending,
    );

    Filament::setTenant($event, isQuiet: true);

    expect($user->canAccessTenant($event))->toBeTrue()
        ->and($user->isOrganizer())->toBeFalse()
        ->and($user->hasPermissions())->toBeFalse();
});

test('new guest is redirected away from an event tenant', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    attachUserToEvent(
        user: $user,
        event: $event,
        status: ParticipantStatus::New,
    );

    expectTenantAccessToRedirect($user, $event);
});

test('rejected guest is redirected away from an event tenant', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    attachUserToEvent(
        user: $user,
        event: $event,
        status: ParticipantStatus::Rejected,
    );

    Filament::setTenant($event, isQuiet: true);

    expectTenantAccessToRedirect($user, $event);

    expect($user->isOrganizer())->toBeFalse()
        ->and($user->hasPermissions())->toBeFalse();
});

test('user outside an event is redirected away from its tenant', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();

    expectTenantAccessToRedirect($user, $event);
});

function expectTenantAccessToRedirect(User $user, Event $event): void
{
    try {
        $user->canAccessTenant($event);

        $this->fail('Tenant access was expected to abort with a redirect response.');
    } catch (HttpResponseException $exception) {
        expect($exception->getResponse()->getTargetUrl())->toBe(url('/dashboard'));
    }
}
