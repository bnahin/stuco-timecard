<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\RestoreEventRequest;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function restore(RestoreEventRequest $request)
    {
        $event = Event::withTrashed()->find($request->id);

        $event->restore();
        log_action('Restored event ' . $event->event_name);

        return response()->json(['status' => 'success']);
    }
}
