<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $logs = ActivityLog::latest()->with('user')->paginate($perPage)->withQueryString();
        return view('backend.activity.index', compact('logs', 'perPage'));
    }
}
