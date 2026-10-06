<?php

namespace App\Http\Controllers;

use App\Models\ClickEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClickTrackController extends Controller
{
    public function track(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:100',
            'event_label' => 'nullable|string|max:255',
            'page_url' => 'nullable|string|max:500',
        ]);

        ClickEvent::create([
            'event_name' => $validated['event_name'],
            'event_label' => $validated['event_label'] ?? null,
            'page_url' => $validated['page_url'] ?? $request->header('referer'),
            'ip_address' => $request->ip(),
            'event_date' => now()->toDateString(),
        ]);

        return response()->json(['success' => true]);
    }
}
