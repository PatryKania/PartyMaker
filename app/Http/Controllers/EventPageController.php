<?php

namespace App\Http\Controllers;

use App\Models\EventPage;
use Illuminate\View\View;

class EventPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = EventPage::with('event')->where('slug', $slug)->firstOrFail();
        
        return view('public.event-page', compact('page'));
    }
}
