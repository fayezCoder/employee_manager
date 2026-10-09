<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $activeEmployeesCount = User::active()->where('role', 'employee')->count();
            $today = Carbon::now('Asia/Riyadh')->toDateString();

            $todayCheckInsCount = AttendanceRecord::where('date', $today)->count();
            $openSessionsCount = AttendanceRecord::whereNull('check_out')->count();

            // Incomplete past records (check_out is null and date < today)
            $incompleteRecords = AttendanceRecord::with('user.department')
                ->whereNull('check_out')
                ->where('date', '<', $today)
                ->orderBy('date', 'asc')
                ->get();

            $incompleteSessionsCount = $incompleteRecords->count();

            return view('dashboard', compact(
                'activeEmployeesCount',
                'todayCheckInsCount',
                'openSessionsCount',
                'incompleteSessionsCount',
                'incompleteRecords'
            ));
        }

        // Employee dashboard view variables
        $todayAttendance = $user->getTodayAttendance();
        $openAttendance = $user->getOpenAttendance();
        $recentRecords = $user->attendanceRecords()->orderBy('date', 'desc')->limit(10)->get();

        return view('dashboard', compact('todayAttendance', 'openAttendance', 'recentRecords'));
    }
}
