<?php

namespace App\Http\Controllers;

use App\Models\LogRequest;
use App\Models\User;
use App\Models\DailyTimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LogRequestController extends Controller
{
    public function create()
    {
        return view('student.log-requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_request' => 'required|date|before_or_equal:today',
            'details' => 'required|string|max:255',
            'proof_file' => 'required|file|max:2048', // Max 2MB file
        ]);

        $proofPath = $request->file('proof_file')->store('proofs', 'public');

        LogRequest::create([
            'student_id' => Auth::id(),
            'date_request' => $request->input('date_request'),
            'details' => $request->input('details'),
            'proof_file_path' => $proofPath,
            'status' => 'pending', // Default status
        ]);

        return redirect()->route('log-requests.index')->with('success', 'Log request submitted successfully.');
    }


    public function studentLogRequests()
    {
        // Fetch the log requests for the authenticated student
        $logRequests = LogRequest::where('student_id', Auth::id())->get();

        return view('student.log-requests.index', compact('logRequests'));
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'date_request' => 'required|date|before_or_equal:today',
            'details' => 'required|string|max:255',
            'proof_file' => 'required|file|max:2048', // Max 2MB file
        ]);

        $proofPath = $request->file('proof_file')->store('proofs', 'public');

        LogRequest::create([
            'student_id' => Auth::id(),
            'date_request' => $request->input('date_request'),
            'details' => $request->input('details'),
            'proof_file_path' => $proofPath,
        ]);

        return redirect()->back()->with('success', 'Log request submitted successfully.');
    }

    public function viewRequests()
    {
        $logRequests = LogRequest::with('student')->get();
        return view('administrative.log-requests.index', compact('logRequests'));
    }

    public function approveRequest(Request $request, $id)
    {
        $logRequest = LogRequest::findOrFail($id);
    
        // Mark the request as approved
        $logRequest->update(['status' => 'approved']);
    
        // Redirect to the log time form with details pre-filled
        return redirect()->route('admin.logTime', ['studentId' => $logRequest->student_id, 'logRequestId' => $logRequest->id]);
    }

    public function rejectRequest(Request $request, $id)
    {
        $logRequest = LogRequest::findOrFail($id);

        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);

        $logRequest->update([
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->back()->with('success', 'Log request rejected.');
    }

    public function adminLogTime(Request $request, $studentId)
    {
        $request->validate([
            'morning_in' => 'nullable|date_format:H:i',
            'morning_out' => 'nullable|date_format:H:i',
            'afternoon_in' => 'nullable|date_format:H:i',
            'afternoon_out' => 'nullable|date_format:H:i',
        ]);
    
        try {
            $student = User::findOrFail($studentId);


            // Fetch the latest DTR record for the student
            $latestRecord = DailyTimeRecord::where('student_id', $student->id)
                                            ->latest('log_date')
                                            ->first();

            if (!$latestRecord) {
            return redirect()->back()->with('error', 'No existing log found for this student.');
            }

            // Extract remaining hours from the latest log
            $previousRemainingHours = $latestRecord->remaining_hours;

    
            $logTimes = [
                'morning_in' => $request->input('morning_in') ? Carbon::createFromFormat('H:i', $request->input('morning_in'))->format('h:i A') : null,
                'morning_out' => $request->input('morning_out') ? Carbon::createFromFormat('H:i', $request->input('morning_out'))->format('h:i A') : null,
                'afternoon_in' => $request->input('afternoon_in') ? Carbon::createFromFormat('H:i', $request->input('afternoon_in'))->format('h:i A') : null,
                'afternoon_out' => $request->input('afternoon_out') ? Carbon::createFromFormat('H:i', $request->input('afternoon_out'))->format('h:i A') : null,
            ];
    
    
            $morningWork = isset($logTimes['morning_in'], $logTimes['morning_out'])
                ? Carbon::parse($logTimes['morning_in'])->diffInHours(Carbon::parse($logTimes['morning_out']), false)
                : 0;
    
            $afternoonWork = isset($logTimes['afternoon_in'], $logTimes['afternoon_out'])
                ? Carbon::parse($logTimes['afternoon_in'])->diffInHours(Carbon::parse($logTimes['afternoon_out']), false)
                : 0;
    
            $continuousWork = isset($logTimes['morning_in'], $logTimes['afternoon_out']) && !isset($logTimes['morning_out'], $logTimes['afternoon_in'])
                ? Carbon::parse($logTimes['morning_in'])->diffInHours(Carbon::parse($logTimes['afternoon_out']), false)
                : $morningWork + $afternoonWork;
    
            $totalHoursWorked = $continuousWork;
    
            $newRemainingHours = $previousRemainingHours - $totalHoursWorked;

            // Update the remaining hours of the latest log
            $latestRecord->update([
                'remaining_hours' => $newRemainingHours,
            ]);
    
            $dailyRecord = DailyTimeRecord::create([
                'student_id' => $student->id,
                'log_date' => $request->input('log_date', Carbon::now()->format('Y-m-d')),
                'log_times' => json_encode($logTimes),
                'total_hours_worked' => $totalHoursWorked,
                'remaining_hours' => $newRemainingHours,
            ]);

            // Update the LogRequest status and remarks
            $logRequestId = $request->input('logRequestId');
            if ($logRequestId) {
                $logRequest = LogRequest::findOrFail($logRequestId);
                $logRequest->update([
                    'status' => 'approved',
                    'remarks' => 'Log Request Accepted',
                ]);
            }

    
            return redirect()->route('admin.log-requests.index')->with('success', 'Time log successfully recorded.');
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while processing the log time: ' . $e->getMessage());
        }
    }
    
}
