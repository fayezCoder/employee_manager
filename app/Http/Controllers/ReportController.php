<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $query = AttendanceRecord::with('user.department');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('date', 'desc')->paginate(15);
        $departments = Department::all();
        $employees = User::all();

        return view('reports.index', compact('records', 'departments', 'employees'));
    }

    public function export(Request $request): StreamedResponse
    {
        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        $query = AttendanceRecord::with('user.department');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('department_id')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="attendance_report.csv"',
        ];

        return response()->stream(function () use ($records) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                __('التاريخ'),
                __('الموظف'),
                __('القسم'),
                __('وقت الحضور'),
                __('وقت الانصراف'),
                __('المدة'),
                __('الحالة')
            ]);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->date->format('Y-m-d'),
                    $this->escapeCsv($record->user->name),
                    $this->escapeCsv($record->user->department->name ?? '-'),
                    $record->getFormattedCheckIn(),
                    $record->getFormattedCheckOut() ?? '-',
                    $record->getFormattedDuration(),
                    $record->getStatusLabel()
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    private function escapeCsv(string $value): string
    {
        if (in_array(substr($value, 0, 1), ['=', '+', '-', '@'])) {
            return "'" . $value;
        }
        return $value;
    }
}
