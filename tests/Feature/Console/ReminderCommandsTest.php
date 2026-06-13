<?php

use App\Enums\ParticipantStatus;
use App\Models\Event;
use App\Models\Participant;
use App\Models\Task;
use App\Models\User;
use App\Notifications\EventNotification;
use App\Notifications\TaskDueNotification;
use Illuminate\Support\Facades\Notification;

test('event reminder command notifies confirmed participants for events one and seven days away', function () {
    Notification::fake();
    $now = now()->setDate(2026, 6, 7)->startOfDay();
    $this->travelTo($now);

    $userForTomorrow = User::factory()->create();
    $userForNextWeek = User::factory()->create();
    $unconfirmedUser = User::factory()->create();
    $tomorrowEvent = Event::factory()->create(['date' => $now->copy()->addDay()]);
    $nextWeekEvent = Event::factory()->create(['date' => $now->copy()->addDays(7)]);
    $otherEvent = Event::factory()->create(['date' => $now->copy()->addDays(3)]);

    Participant::factory()->forUser($userForTomorrow)->confirmed()->create(['event_id' => $tomorrowEvent->id]);
    Participant::factory()->forUser($userForNextWeek)->confirmed()->create(['event_id' => $nextWeekEvent->id]);
    Participant::factory()->forUser($unconfirmedUser)->create([
        'event_id' => $tomorrowEvent->id,
        'status' => ParticipantStatus::Pending,
    ]);
    Participant::factory()->forUser(User::factory()->create())->confirmed()->create(['event_id' => $otherEvent->id]);

    $this->artisan('events:send-reminders')
        ->expectsOutput('Event reminders successfully queued to users!')
        ->assertSuccessful();

    Notification::assertSentTo($userForTomorrow, EventNotification::class);
    Notification::assertSentTo($userForNextWeek, EventNotification::class);
    Notification::assertNotSentTo($unconfirmedUser, EventNotification::class);
});

test('task reminder command notifies assigned users and email-only participants for tasks due in seven days', function () {
    Notification::fake();
    $now = now()->setDate(2026, 6, 7)->startOfDay();
    $this->travelTo($now);

    $event = Event::factory()->create();
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'event_id' => $event->id,
        'due_date' => $now->copy()->addDays(7)->setHour(12),
        'is_completed' => false,
    ]);
    $completedTask = Task::factory()->completed()->create([
        'event_id' => $event->id,
        'due_date' => $now->copy()->addDays(7)->setHour(12),
    ]);
    $assignedParticipant = Participant::factory()->forUser($user)->confirmed()->create(['event_id' => $event->id]);
    $emailOnlyParticipant = Participant::factory()->confirmed()->create([
        'event_id' => $event->id,
        'user_id' => null,
        'email' => 'guest@example.com',
    ]);

    $task->participants()->attach([$assignedParticipant->id, $emailOnlyParticipant->id]);
    $completedTask->participants()->attach($assignedParticipant->id);

    $this->artisan('tasks:send-reminders')
        ->expectsOutput('Sent reminders for 1 tasks.')
        ->assertSuccessful();

    Notification::assertSentTo($user, TaskDueNotification::class);
    Notification::assertSentOnDemand(TaskDueNotification::class, function ($notification, array $channels, object $notifiable) {
        return $notifiable->routes['mail'] === 'guest@example.com';
    });
});

