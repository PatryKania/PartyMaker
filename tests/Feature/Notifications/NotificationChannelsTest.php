<?php

use App\Models\Event;
use App\Models\Participant;
use App\Models\Task;
use App\Models\User;
use App\Notifications\EventNotification;
use App\Notifications\TaskDueNotification;

test('event notification uses mail database and smsapi when participant has phone number', function () {
    $event = Event::factory()->create();
    $user = User::factory()->create();
    Participant::factory()
        ->forUser($user)
        ->confirmed()
        ->create([
            'event_id' => $event->id,
            'phone' => '500600700',
        ]);

    $notification = new EventNotification($event);

    expect($notification->via($user))->toBe(['mail', 'database', 'smsapi'])
        ->and($user->routeNotificationForSmsapi($notification))->toBe('48500600700');
});

test('event notification currently exposes smsapi channel even when participant has no phone', function () {
    $event = Event::factory()->create();
    $user = User::factory()->create();
    Participant::factory()
        ->forUser($user)
        ->confirmed()
        ->create([
            'event_id' => $event->id,
            'phone' => null,
        ]);

    $notification = new EventNotification($event);

    expect($user->routeNotificationForSmsapi($notification))->toBe('48')
        ->and($notification->via($user))->toContain('smsapi');
});

test('task due notification uses mail and database channels', function () {
    $task = Task::factory()->create([
        'title' => 'Confirm catering',
    ]);
    $notification = new TaskDueNotification($task);

    expect($notification->via(User::factory()->create()))->toBe(['mail', 'database'])
        ->and($notification->toDatabase(User::factory()->create()))->toHaveKeys(['title', 'body']);
});

