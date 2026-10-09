<?php

namespace App\Http\Controllers;

use App\Exceptions\AttendanceException;
use App\Http\Requests\CorrectAttendanceRequest;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private AttendanceService $attendanceService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', AttendanceRecord::class);
        $user = $request->user();

        $query = AttendanceRecord::with('user.department');

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('department_id')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                });
            }
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
        $departments = $user->isAdmin() ? Department::all() : collect();
        $employees = $user->isAdmin() ? User::all() : collect();

        return view('attendance.index', compact('records', 'departments', 'employees'));
    }

    public function checkIn(Request $request)
    {
        $this->authorize('checkIn', AttendanceRecord::class);

        try {
            $this->attendanceService->checkIn($request->user());
            return back()->with('success', __('تم تسجيل الحضور بنجاح.'));
        } catch (AttendanceException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('CheckIn error: ' . $e->getMessage());
            return back()->with('error', __('حدث خطأ أثناء تسجيل الحضور.'));
        }
    }

    public function checkOut(Request $request)
    {
        try {
            $this->attendanceService->checkOut($request->user());
            return back()->with('success', __('تم تسجيل الانصراف بنجاح.'));
        } catch (AttendanceException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('CheckOut error: ' . $e->getMessage());
            return back()->with('error', __('حدث خطأ أثناء تسجيل الانصراف.'));
        }
    }

    public function correct(AttendanceRecord $attendance)
    {
        $this->authorize('correct', $attendance);
        return view('attendance.correct', compact('attendance'));
    }

    public function storeCorrection(CorrectAttendanceRequest $request, AttendanceRecord $attendance)
    {
        $this->authorize('correct', $attendance);

        try {
            $this->attendanceService->correctRecord($attendance, $request->user(), $request->validated());
            return redirect()->route('attendance.index')->with('success', __('تم تعديل السجل بنجاح.'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (AttendanceException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Correction error: ' . $e->getMessage());
            return back()->with('error', __('حدث خطأ أثناء التعديل.'));
        }
    }
}
