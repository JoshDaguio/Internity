<?php

namespace App\Http\Controllers;

use App\Models\EndOfDayReport;
use App\Models\DailyTask;
use App\Models\AcceptedInternship;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class EndOfDayReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch the student's accepted internship
        $acceptedInternship = AcceptedInternship::where('student_id', $user->id)->first();
        
        if (!$acceptedInternship) {
            return view('end_of_day_reports.index', [
                'noInternship' => true, // Pass a flag to show a message in the view
            ]);
        }
    
        $startDate = Carbon::parse($acceptedInternship->start_date);
        $schedule = json_decode($acceptedInternship->schedule, true); // Extract schedule details
        $scheduleDays = array_merge($schedule['days'] ?? [], $schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? []); // Combine all schedule days
    
        // Fetch the current date and time from the API or fallback to server time
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
            $currentDateTime = Carbon::parse($response->json('datetime'));
        } catch (\Exception $e) {
            $currentDateTime = Carbon::now(new \DateTimeZone('Asia/Manila'));
        }
    
        $selectedMonth = (int) $request->input('month', $currentDateTime->month);
        $filter = $request->input('filter', 'week');
    
        // Get the list of months with reports or the current month
        $availableMonths = EndOfDayReport::where('student_id', $user->id)
            ->selectRaw('MONTH(date_submitted) as month')
            ->distinct()
            ->pluck('month')
            ->toArray();
    
        // Add the current month if not in the list
        if (!in_array($currentDateTime->month, $availableMonths)) {
            $availableMonths[] = $currentDateTime->month;
        }
    
        $reports = EndOfDayReport::where('student_id', $user->id)
            ->whereMonth('date_submitted', $selectedMonth)
            ->get();
    
        // Calculate missing dates, only start calculating from the Start Date
        $missingDates = $this->getMissingSubmissionDates($user->id, $selectedMonth, $currentDateTime, $scheduleDays, $startDate);
        
        // Check if today is in the student's schedule and after the start date
        $isScheduledDay = in_array($currentDateTime->format('l'), $scheduleDays) && $currentDateTime->gte($startDate);
    
        // Check if a report has already been submitted today
        $hasSubmittedToday = EndOfDayReport::where('student_id', $user->id)
            ->whereDate('date_submitted', $currentDateTime->format('Y-m-d'))
            ->exists();

        // Set $noInternship to false as there is an internship
        $noInternship = false;
    
        // Pass variables to the view, including the schedule days
        return view('end_of_day_reports.index', compact(
            'reports', 
            'missingDates', 
            'selectedMonth', 
            'availableMonths', 
            'hasSubmittedToday', 
            'isScheduledDay', 
            'startDate', 
            'scheduleDays',
            'acceptedInternship',
            'currentDateTime',
            'noInternship'
        ));
    }

    private function getMissingSubmissionDates($studentId, $selectedMonth, $currentDateTime, $scheduleDays, $startDate)
    {
        $startOfMonth = Carbon::create($currentDateTime->year, $selectedMonth, 1)->startOfMonth();
        $today = $selectedMonth == $currentDateTime->month ? $currentDateTime->copy() : $startOfMonth->copy()->endOfMonth();
    
        // Only check dates starting from the Start Date
        if ($startDate->gt($startOfMonth)) {
            $startOfMonth = $startDate->copy();
        }
    
        // Get all scheduled days between the start of the month and today
        $allScheduledDays = collect();
        for ($date = $startOfMonth; $date->lte($today); $date->addDay()) {
            if (in_array($date->format('l'), $scheduleDays)) {
                $allScheduledDays->push($date->copy()->format('Y-m-d'));
            }
        }
    
        // Get submission dates for the selected month
        $submissionDates = EndOfDayReport::where('student_id', $studentId)
            ->whereYear('date_submitted', $currentDateTime->year)
            ->whereMonth('date_submitted', $selectedMonth)
            ->pluck('date_submitted')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });
    
        // Calculate missing dates
        return $allScheduledDays->diff($submissionDates);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Fetch the current date and time from the API or fallback to server time
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
            $currentDateTime = Carbon::parse($response->json('datetime'));
        } catch (\Exception $e) {
            $currentDateTime = Carbon::now(new \DateTimeZone('Asia/Manila'));
        }

        return view('end_of_day_reports.create', compact('currentDateTime'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'key_successes' => 'required|string',
            'main_challenges' => 'required|string',
            'plans_for_tomorrow' => 'required|string',
            'tasks' => 'required|array',
            'tasks.*.task_description' => 'required|string',
            'tasks.*.time_spent' => 'required|integer|min:1',
            'tasks.*.time_unit' => 'required|in:minutes,hours',
        ]);

        // Fetch the current date and time from a reliable source
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
            $currentDateTime = Carbon::parse($response->json('datetime'));
        } catch (\Exception $e) {
            // Fallback to server time if API fails
            $currentDateTime = Carbon::now(new \DateTimeZone('Asia/Manila'));
        }

        $report = EndOfDayReport::create([
            'student_id' => Auth::id(),
            'key_successes' => $request->key_successes,
            'main_challenges' => $request->main_challenges,
            'plans_for_tomorrow' => $request->plans_for_tomorrow,
            'date_submitted' => $currentDateTime,
        ]);

        foreach ($request->tasks as $task) {
            DailyTask::create([
                'report_id' => $report->id,
                'task_description' => $task['task_description'],
                'time_spent' => $task['time_spent'],
                'time_unit' => $task['time_unit'],
            ]);
        }

        return redirect()->route('end_of_day_reports.index')->with('success', 'Report submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $report = EndOfDayReport::with('tasks')->findOrFail($id);

        // Check if the user is a student and is trying to view their own report
        if (Auth::user()->role_id == 5) { 
            if ($report->student_id != Auth::id()) {
                return redirect()->route('end_of_day_reports.index')->with('error', 'Unauthorized access.');
            }
        } elseif (in_array(Auth::user()->role_id, [1, 2, 3])) {
            // Check if the user is a faculty member and is only viewing reports for their course
            if (Auth::user()->role_id == 3 && $report->student->course_id != Auth::user()->course_id) {
                return redirect()->route('end_of_day_reports.index')->with('error', 'Unauthorized access.');
            }
        } else {
            // Unauthorized access for other roles
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
    
        return view('end_of_day_reports.show', compact('report'));
    }

    public function compileMonthly()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $reports = EndOfDayReport::where('student_id', Auth::id())
            ->whereMonth('date_submitted', $currentMonth)
            ->whereYear('date_submitted', $currentYear)
            ->orderBy('date_submitted', 'asc')
            ->get();

        return view('end_of_day_reports.monthly_compilation', compact('reports', 'currentMonth', 'currentYear'));
    }

    public function downloadMonthlyPDF()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $reports = EndOfDayReport::where('student_id', Auth::id())
            ->whereMonth('date_submitted', $currentMonth)
            ->whereYear('date_submitted', $currentYear)
            ->orderBy('date_submitted', 'asc')
            ->get();

        $profile = Auth::user()->profile;
        $studentName = $profile->last_name . ', ' . $profile->first_name;
        $pdf = Pdf::loadView('end_of_day_reports.pdf.monthly_compilation', compact('reports', 'currentMonth', 'currentYear', 'studentName'));
        
        return $pdf->download("{$studentName}_{$currentMonth}_{$currentYear}_Monthly_Report.pdf");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function compileWeekly()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $reports = EndOfDayReport::where('student_id', Auth::id())
            ->whereBetween('date_submitted', [$startOfWeek, $endOfWeek])
            ->orderBy('date_submitted', 'asc')
            ->get();

        return view('end_of_day_reports.weekly_compilation', compact('reports', 'startOfWeek', 'endOfWeek'));
    }

    public function downloadWeeklyPDF()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $reports = EndOfDayReport::where('student_id', Auth::id())
            ->whereBetween('date_submitted', [$startOfWeek, $endOfWeek])
            ->orderBy('date_submitted', 'asc')
            ->get();

        $profile = Auth::user()->profile;
        $studentName = $profile->last_name . ', ' . $profile->first_name;
        $pdf = Pdf::loadView('end_of_day_reports.pdf.weekly_compilation', compact('reports', 'startOfWeek', 'endOfWeek', 'studentName'));
        
        return $pdf->download("{$studentName}_Weekly_Report.pdf");
    }
}
