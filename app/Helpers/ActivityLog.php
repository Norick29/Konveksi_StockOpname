<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

if (!function_exists('logActivity')) {
    function logActivity(
        string $action,
        string $module,
        string $description,
        array $data = []
    ) {
        ActivityLog::create([
            'id_user' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'data' => $data
        ]);
    }
}