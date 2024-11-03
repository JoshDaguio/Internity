<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\User;
use App\Models\AcceptedInternship;
use App\Models\Penalty;
use App\Models\PenaltiesAwarded;
use App\Models\DailyTimeRecord;
use App\Models\AcademicYear;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\RequestStatusNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RequestController extends Controller
{
    // Display all requests for Admins
    public function adminIndex(HttpRequest $request)
    {
        $academicYears = AcademicYear::all();
        $selectedYearId = $request->input('academic_year_id', AcademicYear::where('is_current', true)->first()->id);
        $statusFilter = $request->input('status', 'all');
    
        // Fetch requests based on the selected academic year and status filter
        $requests = Request::where('academic_year_id', $selectedYearId)
            ->with('student.profile');

        // Apply the status filter if it's not 'all'
        if ($statusFilter !== 'all') {
            $requests->where('status', $statusFilter);
        }

        $requests = $requests->get(); // Execute the query and get results

        return view('requests.admin_index', compact('requests', 'academicYears', 'selectedYearId', 'statusFilter'));
    }

    // Show request details for Admins
    public function show(Request $request)
    {
        $acceptedInternship = AcceptedInternship::where('student_id', $request->student_id)->first();
        return view('requests.show', compact('request', 'acceptedInternship'));
    }

    // Student: Display all submitted requests
    public function studentIndex()
    {
        $requests = Request::where('student_id', Auth::id())->get();
        return view('requests.student_index', compact('requests'));
    }

    // Student: Show create form for excusal request
    public function create()
    {
        return view('requests.create');
    }


    // Store a new excusal request by Students
    public function store(HttpRequest $httpRequest)
    {
        $httpRequest->validate([
            'reason' => 'required|string',
            'absence_date' => 'required|date',
            'attachment' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
        ]);

        $attachmentPath = $httpRequest->file('attachment')
            ? $httpRequest->file('attachment')->store('attachments')
            : null;

        $currentAcademicYear = AcademicYear::where('is_current', true)->firstOrFail();

        Request::create([
            'student_id' => Auth::id(),
            'reason' => $httpRequest->reason,
            'absence_date' => $httpRequest->absence_date,
            'attachment_path' => $attachmentPath,
            'academic_year_id' => $currentAcademicYear->id,
        ]);

        return redirect()->route('requests.studentIndex')->with('success', 'Request submitted successfully.');
    }

    // Student: Show a specific request with details and status
    public function studentShow(Request $request)
    {
        $student = Auth::user();

        return view('requests.student_show', compact('request','student'));
    }

    // Company: Display approved requests
    public function companyIndex(HttpRequest $request)
    {
        $company = Auth::user();
        $academicYears = AcademicYear::all();
        $selectedYearId = $request->input('academic_year_id', AcademicYear::where('is_current', true)->first()->id);

        $requests = Request::whereHas('student.acceptedInternship', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })
            ->where('status', 'approved')
            ->where('academic_year_id', $selectedYearId)
            ->get();

        return view('requests.company_index', compact('requests', 'academicYears', 'selectedYearId'));
    }

    public function companyShow(Request $request)
    {
        $acceptedInternship = AcceptedInternship::where('student_id', $request->student_id)->first();
        return view('requests.company_show', compact('request', 'acceptedInternship'));
    }
    

    // Admin responding to the request
    public function respond(HttpRequest $httpRequest, Request $request)
    {
        $httpRequest->validate([
            'penalty_type' => 'required|in:excused_absence,unexcused_absence',
            'admin_remarks' => 'nullable|string',
        ]);

        $requestStatus = $httpRequest->status;
        $penaltyType = $httpRequest->penalty_type;
        $student = $request->student;

        if ($httpRequest->status === 'approved') {
            $penalty = Penalty::where('violation', ucfirst(str_replace('_', ' ', $httpRequest->penalty_type)))->first();

            // Apply penalty and adjust remaining hours
            $dailyRecord = DailyTimeRecord::where('student_id', $request->student_id)->latest('log_date')->first();
            $dailyRecord->remaining_hours += $penalty->penalty_hours;
            $dailyRecord->save();

            PenaltiesAwarded::create([
                'student_id' => $request->student_id,
                'penalty_id' => $penalty->id,
                'dtr_id' => $dailyRecord->id,
                'penalty_hours' => $penalty->penalty_hours,
                'awarded_date' => Carbon::now(),
                'remarks' => 'Excusal request ' . ucfirst($httpRequest->penalty_type),
            ]);

            $request->update([
                'status' => 'approved',
                'penalty_type' => $httpRequest->penalty_type,
                'admin_remarks' => $httpRequest->admin_remarks,
            ]);

            // Notify the company
            $acceptedInternship = AcceptedInternship::where('student_id', $request->student_id)->first();
            if ($acceptedInternship) {
                Mail::to($acceptedInternship->company->email)->send(new \App\Mail\ExcusalNotification($request));
            }
        } else {
            $request->update(['status' => 'rejected', 'admin_remarks' => $httpRequest->admin_remarks]);
        }

        // Notify the student of the status update
        Mail::to($student->email)->send(new RequestStatusNotification($request, $requestStatus));

        return redirect()->route('requests.adminIndex')->with('success', 'Request processed.');
    }

    public function preview($id)
    {
        $request = Request::findOrFail($id);

        if ($request->attachment_path && Storage::exists($request->attachment_path)) {
            $filePath = storage_path('app/' . $request->attachment_path);
            return response()->file($filePath);
        } else {
            abort(404, 'File not found.');
        }
    }

}

