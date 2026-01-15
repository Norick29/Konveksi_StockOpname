<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogs extends Controller
{
    public function index(Request $request)
{
    $logs = ActivityLog::with('user')
        ->when($request->user_id, fn ($q) =>
            $q->where('user_id', $request->user_id)
        )
        ->when($request->action, fn ($q) =>
            $q->where('action', $request->action)
        )
        ->orderByDesc('created_at')
        ->paginate(15);

    return view('modul.Activity.index', compact('logs'));
}
}
