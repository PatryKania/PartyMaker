<?php

use App\Filament\EventPanel\Resources\EventPages\EventPageResource;
use App\Filament\EventPanel\Resources\Gifts\GiftResource;
use App\Filament\EventPanel\Resources\Invitations\InvitationResource;
use App\Filament\EventPanel\Resources\Participants\ParticipantResource;
use App\Filament\EventPanel\Resources\Schedules\ScheduleResource;
use App\Filament\EventPanel\Resources\Surveys\SurveyResource;
use App\Filament\EventPanel\Resources\Tasks\TaskResource;
use App\Models\Event;
use Filament\Facades\Filament;

afterEach(function () {
    Filament::setTenant(null, isQuiet: true);
});

test('core tenant resources can resolve event relationships used by filament tenancy', function () {
    $event = Event::factory()->create();
    Filament::setTenant($event, isQuiet: true);

    foreach ([ParticipantResource::class, SurveyResource::class, TaskResource::class] as $resource) {
        expect($resource::getTenantRelationship($event)->getRelated()->getTable())->not->toBeEmpty();
    }
});

test('some tenant resources currently miss matching plural relationships on event model', function () {
    $event = Event::factory()->create();
    Filament::setTenant($event, isQuiet: true);

    foreach ([GiftResource::class, ScheduleResource::class, InvitationResource::class, EventPageResource::class] as $resource) {
        expect(fn () => $resource::getTenantRelationship($event))
            ->toThrow(LogicException::class);
    }
});
