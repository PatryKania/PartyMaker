<?php

namespace App\Http\Controllers;

use App\Models\EventPage;
use Illuminate\View\View;
use App\Filament\Resources\Events\EventResource;

class EventPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = EventPage::with('event')->where('slug', $slug)->firstOrFail();
        $eventURL = route('filament.event.pages.event-dashboard', ['tenant' => $page->event->id]);
        return view('public.event-page', compact('page','eventURL'));
    }
}
