<?php

use App\Models\Event;
use App\Models\EventPage;

test('public event page is displayed for an existing slug', function () {
    $event = Event::factory()->create([
        'name' => 'Wedding Party',
        'color' => '#f59e0b',
    ]);
    $page = EventPage::factory()->create([
        'event_id' => $event->id,
        'slug' => 'wedding-party',
        'content' => [
            'pl' => '<h1>Witaj na weselu</h1>',
            'en' => '<h1>Welcome to the wedding</h1>',
        ],
        'down_content' => [
            'pl' => '<p>Dolna sekcja</p>',
            'en' => '<p>Bottom section</p>',
        ],
    ]);

    $this->get(route('public.event.show', $page->slug))
        ->assertOk()
        ->assertSee('Wedding Party')
        ->assertSee('Witaj na weselu', false);
});

test('public event page returns 404 for an unknown slug', function () {
    $this->get(route('public.event.show', 'missing-slug'))
        ->assertNotFound();
});

