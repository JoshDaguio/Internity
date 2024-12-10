<?php

namespace App\Http\Controllers;

use App\Models\OTRequest;
use App\Models\User;
use App\Models\DailyTimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OTRequestController extends Controller
{
    public function create()
    {
        return view('student.ot-requests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_request' => 'required|date|before_or_equal:today',
            'ot_start_time' => 'required|date_format:H:i',
            'ot_end_time' => 'required|date_format:H:i|after:ot_start_time',
            'details' => 'required|string|max:255',
            'proof_file' => 'required|file|max:2048', // Max 2MB file
        ]);

        $proofPath = $request->file('proof_file')->store('proofs', 'public');

        OTRequest::create([
            'student_id' => Auth::id(),
            'date_request' => $request->input('date_request'),
            'ot_start_time' => $request->input('ot_start_time'),
            'ot_end_time' => $request->input('ot_end_time'),
            'details' => $request->input('details'),
            'proof_file_path' => $proofPath,
            'status' => 'pending', // Default status
        ]);

        return redirect()->route('ot-requests.index')->with('success', 'OT request submitted successfully.');
    }

    public function studentOTRequests()
    {
        $otRequests = OTRequest::where('student_id', Auth::id())->get();
        return view('student.ot-requests.index', compact('otRequests'));
    }

    public function viewRequests()
    {
        $otRequests = OTRequest::with('student')->get();
        return view('administrative.ot-requests.index', compact('otRequests'));
    }

    public function approveRequest(Request $request, $id)
    {
        $otRequest = OTRequest::findOrFail($id);

        $otHours = Carbon::parse($otRequest->ot_start_time)->diffInHours(Carbon::parse($otRequest->ot_end_time));

        // Update the latest DailyTimeRecord for the student
        $latestRecord = DailyTimeRecord::where('student_id', $otRequest->student_id)
                                        ->latest('log_date')
                                        ->first();

        if ($latestRecord) {
            $remainingHours = max($latestRecord->remaining_hours - $otHours, 0);
            $additionalOTHours = max($latestRecord->total_hours_worked + $otHours, 0 );
            $latestRecord->update(['remaining_hours' => $remainingHours,'total_hours_worked' => $additionalOTHours]);
        }

        // Update OT Request status and remarks
        $otRequest->update([
            'status' => 'approved',
            'remarks' => 'Overtime Request Accepted',
        ]);

        return redirect()->route('admin.ot-requests.index')->with('success', 'OT request approved and hours deducted successfully.');
    }

    public function rejectRequest(Request $request, $id)
    {
        $otRequest = OTRequest::findOrFail($id);

        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);

        $otRequest->update([
            'status' => 'rejected',
            'remarks' => $request->input('remarks'),
        ]);

        return redirect()->back()->with('success', 'OT request rejected.');
    }
}
