<?php

namespace App\Http\Controllers;

use App\Models\AttendanceAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $logs = AttendanceAuditLog::with(['admin', 'attendanceRecord.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('audit-logs.index', compact('logs'));
    }
}
