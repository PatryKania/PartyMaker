<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Event;
use Illuminate\Support\Facades\URL;

class SetCurrentEvent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $eventId = $request->route('event');

        if ($eventId) {
            session(['current_event_id' => (int) $eventId]);
            $event = Event::findOrFail($eventId);
            app()->instance('currentEvent', $event);
            URL::defaults(['event' => $event->id]);
        }
        return $next($request);
    }
}
