<?php

namespace App\Http\Controllers;

use App\Models\EndOfDayReport;
use App\Models\DailyTask;
use App\Models\User;
use App\Models\AcceptedInternship;
use App\Models\MonthlyReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;


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

        // Ensure the schedule is decoded only if it's a string (JSON), otherwise use it directly as an array
        $schedule = is_array($acceptedInternship->schedule) 
            ? $acceptedInternship->schedule 
            : json_decode($acceptedInternship->schedule, true);

        // Check if the student is irregular and has a custom schedule
        if ($user->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = is_array($acceptedInternship->custom_schedule) 
                ? $acceptedInternship->custom_schedule 
                : json_decode($acceptedInternship->custom_schedule, true);
            $scheduleDays = array_keys($customSchedule); // Use custom schedule for irregular students
        } else {
            // Use standard schedule for regular students
            $scheduleDays = array_merge($schedule['days'] ?? [], $schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? []);
        }

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

        // Count late submissions
        $lateSubmissions = $reports->where('is_late', true)->count();

        // Calculate missing dates, only start calculating from the Start Date
        $missingDates = $this->getMissingSubmissionDates($user->id, $selectedMonth, $currentDateTime, $scheduleDays, $startDate);
        
        // Check if today is in the student's schedule and after the start date
        $isScheduledDay = in_array($currentDateTime->format('l'), $scheduleDays) && $currentDateTime->gte($startDate);

        // Check if a report has already been submitted today
        $hasSubmittedToday = EndOfDayReport::where('student_id', $user->id)
            ->whereDate('submission_for_date', $currentDateTime->format('Y-m-d'))
            ->exists();

        // Set $noInternship to false as there is an internship
        $noInternship = false;

        // Pass variables to the view
        return view('end_of_day_reports.index', compact(
            'user', 
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
            'noInternship',
            'lateSubmissions'
        ));
    }

    
    private function getMissingSubmissionDates($studentId, $selectedMonth, $currentDateTime, $scheduleDays, $startDate)
    {
        // Start date for checking missing submissions (internship start date or earlier month)
        $start = $startDate->copy();

        // Set the end date to today or the end of the month
        $end = $currentDateTime;

        // Collect all scheduled days between the internship start date and today
        $allScheduledDays = collect();
        for ($date = $start; $date->lte($end); $date->addDay()) {
            if (in_array($date->format('l'), $scheduleDays)) {
                $allScheduledDays->push($date->copy()->format('Y-m-d'));
            }
        }

        // Get submission dates for all months since the internship started
        $submissionDates = EndOfDayReport::where('student_id', $studentId)
            ->whereDate('date_submitted', '>=', $startDate) // Start fetching from internship start date
            ->whereDate('date_submitted', '<=', $end)       // End with the current date
            ->pluck('date_submitted')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });

        // Calculate missing dates by subtracting submitted dates from all scheduled days
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
            'tasks.*.time_hours' => 'nullable|integer|min:0',
            'tasks.*.time_minutes' => 'nullable|integer|min:0|max:59',
        ]);

        // Fetch the current date and time from a reliable source
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
            $currentDateTime = Carbon::parse($response->json('datetime'));
        } catch (\Exception $e) {
            // Fallback to server time if API fails
            $currentDateTime = Carbon::now(new \DateTimeZone('Asia/Manila'));
        }

        // Use the provided submission date or fallback to the current date
        $submissionForDate = $request->input('submission_date') 
            ? Carbon::parse($request->input('submission_date'))->toDateString()
            : $currentDateTime->toDateString();
            
        // Determine if the submission is late
        $isLate = $submissionForDate !== $currentDateTime->toDateString();

        $report = EndOfDayReport::create([
            'student_id' => Auth::id(),
            'key_successes' => $request->key_successes,
            'main_challenges' => $request->main_challenges,
            'plans_for_tomorrow' => $request->plans_for_tomorrow,
            'date_submitted' => $currentDateTime,
            'submission_for_date' => $submissionForDate,                
            'is_late' => $isLate,
        ]);

        foreach ($request->tasks as $task) {
            $timeInMinutes = ($task['time_hours'] ?? 0) * 60 + ($task['time_minutes'] ?? 0);

            DailyTask::create([
                'report_id' => $report->id,
                'task_description' => $task['task_description'],
                'time_spent' => $timeInMinutes, // store in minutes
                'time_unit' => 'minutes', // always store in minutes
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

    public function studentEOD($studentId, Request $request)
    {
        $student = User::findOrFail($studentId);
        $currentYear = Carbon::now()->year;

        // Fetch the student's accepted internship
        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();
        if (!$acceptedInternship) {
            return view('end_of_day_reports.monthly_compilation', [
                'noInternship' => true,
            ]);
        }

        $startDate = Carbon::parse($acceptedInternship->start_date);

        // Get all available months from internship start date to the current month
        $availableMonths = [];
        for ($month = $startDate->month; $month <= Carbon::now()->month; $month++) {
            $availableMonths[] = $month;
        }

        // Set selected month from request, defaulting to the current month
        $selectedMonth = (int) $request->input('month', Carbon::now()->month);

        // Set the start and end dates for the selected month
        if ($selectedMonth == $startDate->month && $startDate->year == $currentYear) {
            if (Carbon::now()->month == $startDate->month) {
                $startOfMonth = $startDate;
                $endOfMonth = Carbon::now();
            } else {
                $startOfMonth = $startDate;
                $endOfMonth = $startDate->copy()->endOfMonth();  // End at the end of the selected month (September 30)
            }
        } elseif ($selectedMonth == Carbon::now()->month) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now();
        } else {
            $startOfMonth = Carbon::create($currentYear, $selectedMonth, 1)->startOfMonth();
            $endOfMonth = Carbon::create($currentYear, $selectedMonth, 1)->endOfMonth();
        }

        // Decode the schedule and handle student schedule logic
        $schedule = is_array($acceptedInternship->schedule)
            ? $acceptedInternship->schedule
            : json_decode($acceptedInternship->schedule, true);

        if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = is_array($acceptedInternship->custom_schedule)
                ? $acceptedInternship->custom_schedule
                : json_decode($acceptedInternship->custom_schedule, true);
            $scheduleDays = array_keys($customSchedule);
        } else {
            $scheduleDays = array_merge($schedule['days'] ?? [], $schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? []);
        }

        // Fetch all reports submitted in the selected month
        $reports = EndOfDayReport::where('student_id', $student->id)
            ->whereBetween('submission_for_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('submission_for_date', 'asc')
            ->get();


        // Label late submissions
        foreach ($reports as $report) {
            $report->is_late = $report->submission_for_date !== $report->date_submitted->toDateString();
        }

        // Get missing submission dates for the selected month
        $missingDates = $this->getMissingMonthlySubmissionDates($student->id, $startOfMonth, $endOfMonth, $scheduleDays);

        // If no reports exist, we only display the missing dates
        if ($reports->isEmpty()) {
            return view('end_of_day_reports.student-eod', [
                'reports' => collect([]), // Empty collection to avoid errors
                'missingDates' => $missingDates,
                'noReports' => true,
                'selectedMonth' => $selectedMonth,
                'availableMonths' => $availableMonths,
                'currentYear' => $currentYear,
                'student' => $student, 
            ]);
        }

        return view('end_of_day_reports.student-eod', compact('reports', 'selectedMonth', 'currentYear', 'missingDates', 'availableMonths','student'));

    }



    //For Monthly Reports
    private function getMissingMonthlySubmissionDates($studentId, $startOfMonth, $endOfMonth, $scheduleDays)
    {
        $start = $startOfMonth->copy();
        $end = $endOfMonth->copy();

        // Collect all scheduled days between the selected month range
        $allScheduledDays = collect();
        for ($date = $start; $date->lte($end); $date->addDay()) {
            if (in_array($date->format('l'), $scheduleDays)) {
                $allScheduledDays->push($date->copy()->format('Y-m-d'));
            }
        }

        // Get submission dates for the selected month
        $submissionDates = EndOfDayReport::where('student_id', $studentId)
            ->whereBetween('date_submitted', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->pluck('date_submitted')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });

        // Calculate missing dates by subtracting submitted dates from all scheduled days
        return $allScheduledDays->diff($submissionDates)->unique();
    }


    public function compileMonthly(Request $request)
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        // Fetch the student's accepted internship
        $acceptedInternship = AcceptedInternship::where('student_id', $user->id)->first();
        if (!$acceptedInternship) {
            return view('end_of_day_reports.monthly_compilation', [
                'noInternship' => true,
            ]);
        }

        $startDate = Carbon::parse($acceptedInternship->start_date);

        // Get all available months from internship start date to the current month
        $availableMonths = [];
        for ($month = $startDate->month; $month <= Carbon::now()->month; $month++) {
            $availableMonths[] = $month;
        }

        // Set selected month from request, defaulting to the current month
        $selectedMonth = (int) $request->input('month', Carbon::now()->month);

        // Set the start and end dates for the selected month
        if ($selectedMonth == $startDate->month && $startDate->year == $currentYear) {
            if (Carbon::now()->month == $startDate->month) {
                $startOfMonth = $startDate;
                $endOfMonth = Carbon::now();
            } else {
                $startOfMonth = $startDate;
                $endOfMonth = $startDate->copy()->endOfMonth();  // End at the end of the selected month (September 30)
            }
        } elseif ($selectedMonth == Carbon::now()->month) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now();
        } else {
            $startOfMonth = Carbon::create($currentYear, $selectedMonth, 1)->startOfMonth();
            $endOfMonth = Carbon::create($currentYear, $selectedMonth, 1)->endOfMonth();
        }

        // Decode the schedule and handle student schedule logic
        $schedule = is_array($acceptedInternship->schedule)
            ? $acceptedInternship->schedule
            : json_decode($acceptedInternship->schedule, true);

        if ($user->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = is_array($acceptedInternship->custom_schedule)
                ? $acceptedInternship->custom_schedule
                : json_decode($acceptedInternship->custom_schedule, true);
            $scheduleDays = array_keys($customSchedule);
        } else {
            $scheduleDays = array_merge($schedule['days'] ?? [], $schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? []);
        }

        // Fetch all reports submitted in the selected month
        $reports = EndOfDayReport::where('student_id', $user->id)
            ->whereBetween('submission_for_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('submission_for_date', 'asc')
            ->get();

            
        // Label late submissions
        foreach ($reports as $report) {
            $report->is_late = $report->submission_for_date !== $report->date_submitted->toDateString();
        }

        // Get missing submission dates for the selected month
        $missingDates = $this->getMissingMonthlySubmissionDates($user->id, $startOfMonth, $endOfMonth, $scheduleDays);

        // If no reports exist, we only display the missing dates
        if ($reports->isEmpty()) {
            return view('end_of_day_reports.monthly_compilation', [
                'reports' => collect([]), // Empty collection to avoid errors
                'missingDates' => $missingDates,
                'noReports' => true,
                'selectedMonth' => $selectedMonth,
                'availableMonths' => $availableMonths,
                'currentYear' => $currentYear,
            ]);
        }

        return view('end_of_day_reports.monthly_compilation', compact('reports', 'selectedMonth', 'currentYear', 'missingDates', 'availableMonths'));
    }

    

    public function downloadMonthlyPDF(Request $request)
    {
        $user = Auth::user();
        $currentYear = Carbon::now()->year;

        // Fetch the student's accepted internship
        $acceptedInternship = AcceptedInternship::where('student_id', $user->id)->first();
        if (!$acceptedInternship) {
            return view('end_of_day_reports.monthly_compilation', [
                'noInternship' => true,
            ]);
        }

        $startDate = Carbon::parse($acceptedInternship->start_date);

        // Get all available months from internship start date to the current month
        $availableMonths = [];
        for ($month = $startDate->month; $month <= Carbon::now()->month; $month++) {
            $availableMonths[] = $month;
        }

        // Set selected month from request, defaulting to the current month
        $selectedMonth = (int) $request->input('month', Carbon::now()->month);

        // Set the start and end dates for the selected month
        if ($selectedMonth == $startDate->month && $startDate->year == $currentYear) {
            if (Carbon::now()->month == $startDate->month) {
                $startOfMonth = $startDate;
                $endOfMonth = Carbon::now();
            } else {
                $startOfMonth = $startDate;
                $endOfMonth = $startDate->copy()->endOfMonth();  // End at the end of the selected month (September 30)
            }
        } elseif ($selectedMonth == Carbon::now()->month) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now();
        } else {
            $startOfMonth = Carbon::create($currentYear, $selectedMonth, 1)->startOfMonth();
            $endOfMonth = Carbon::create($currentYear, $selectedMonth, 1)->endOfMonth();
        }

        // Decode the schedule and handle student schedule logic
        $schedule = is_array($acceptedInternship->schedule)
            ? $acceptedInternship->schedule
            : json_decode($acceptedInternship->schedule, true);

        if ($user->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = is_array($acceptedInternship->custom_schedule)
                ? $acceptedInternship->custom_schedule
                : json_decode($acceptedInternship->custom_schedule, true);
            $scheduleDays = array_keys($customSchedule);
        } else {
            $scheduleDays = array_merge($schedule['days'] ?? [], $schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? []);
        }

        // Fetch all reports submitted in the selected month
        $reports = EndOfDayReport::where('student_id', $user->id)
            ->whereBetween('submission_for_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->orderBy('submission_for_date', 'asc')
            ->get();

        // Label late submissions
        foreach ($reports as $report) {
            $report->is_late = $report->submission_for_date !== $report->date_submitted->toDateString();
        }

        // Get missing submission dates for the selected month
        $missingDates = $this->getMissingMonthlySubmissionDates($user->id, $startOfMonth, $endOfMonth, $scheduleDays);

        $profile = Auth::user()->profile;
        $studentName = $profile->last_name . ', ' . $profile->first_name;
   
        $pdf = Pdf::loadView('end_of_day_reports.pdf.monthly_compilation', compact('reports', 'selectedMonth', 'currentYear', 'studentName', 'missingDates'));
        $fileName = "{$studentName}_{$selectedMonth}_{$currentYear}_Monthly_Report.pdf";

        // Store the PDF file
        $filePath = "monthly_reports/{$user->id}/eod/{$fileName}";
        Storage::put($filePath, $pdf->output());

        // Save the file path to the monthly_reports table if it doesn't exist
        $monthYearDate = Carbon::create($currentYear, $selectedMonth, 1)->startOfMonth()->format('Y-m-d');
        MonthlyReport::updateOrCreate(
            [
                'student_id' => $user->id,
                'type' => 'eod',
                'month_year' => $monthYearDate,
            ],
            [
                'file_path' => $filePath,
            ]
        );

        return $pdf->download($fileName);
    }

    //For Weekly
    private function getMissingWeeklySubmissionDates($studentId, $startOfWeek, $endOfWeek, $scheduleDays)
    {
        $start = $startOfWeek->copy();
        $end = $endOfWeek->copy();
        
        // Collect all scheduled days between the internship start date and today
        $allScheduledDays = collect();
        for ($date = $start; $date->lte($end); $date->addDay()) {
            if (in_array($date->format('l'), $scheduleDays)) {
                $allScheduledDays->push($date->copy()->format('Y-m-d'));
            }
        }

        // Get submission dates for the given week
        $submissionDates = EndOfDayReport::where('student_id', $studentId)
            ->whereBetween('date_submitted', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->pluck('date_submitted')
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });

        // Calculate missing dates by subtracting submitted dates from all scheduled days
        return $allScheduledDays->diff($submissionDates)->values();
    }

    public function compileWeekly()
    {
        $user = Auth::user();
        
        // Set the current date as end of the week and calculate the start date (last 7 days)
        $endOfWeek = Carbon::now(); // Today is the end of the week
        $startOfWeek = Carbon::now()->subDays(6); // 7 days range, including today
    
        // Fetch the student's accepted internship
        $acceptedInternship = AcceptedInternship::where('student_id', $user->id)->first();
        if (!$acceptedInternship) {
            return view('end_of_day_reports.weekly_compilation', [
                'noInternship' => true,
            ]);
        }
    
        $startDate = Carbon::parse($acceptedInternship->start_date);
    
        // Decode the schedule and use it based on the student's regular/irregular status
        $schedule = is_array($acceptedInternship->schedule)
            ? $acceptedInternship->schedule
            : json_decode($acceptedInternship->schedule, true);
    
        if ($user->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = is_array($acceptedInternship->custom_schedule)
                ? $acceptedInternship->custom_schedule
                : json_decode($acceptedInternship->custom_schedule, true);
            $scheduleDays = array_keys($customSchedule);
        } else {
            $scheduleDays = array_merge($schedule['days'] ?? [], $schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? []);
        }
    
        // Fetch all reports submitted in the last 7 days
        $reports = EndOfDayReport::where('student_id', $user->id)
            ->whereBetween('submission_for_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->orderBy('submission_for_date', 'asc')
            ->get();

        // Label late submissions
        foreach ($reports as $report) {
            $report->is_late = $report->submission_for_date !== $report->date_submitted->toDateString();
        }

        // Get missing submission dates within this week (last 7 days)
        $missingDates = $this->getMissingWeeklySubmissionDates($user->id, $startOfWeek, $endOfWeek, $scheduleDays);

        // Check if reports are empty and send appropriate message
        if ($reports->isEmpty()) {
            return view('end_of_day_reports.weekly_compilation', [
                'noReports' => true,
                'startOfWeek' => $startOfWeek,
                'endOfWeek' => $endOfWeek,
                'missingDates' => $missingDates,
            ]);
        }
    
        return view('end_of_day_reports.weekly_compilation', compact('reports', 'startOfWeek', 'missingDates', 'endOfWeek',  'scheduleDays'));
    }
    
    public function downloadWeeklyPDF()
    {
        $user = Auth::user();
        
        // Set the current date as end of the week and calculate the start date (last 7 days)
        $endOfWeek = Carbon::now(); // Today is the end of the week
        $startOfWeek = Carbon::now()->subDays(6); // 7 days range, including today
    
        // Fetch the student's accepted internship
        $acceptedInternship = AcceptedInternship::where('student_id', $user->id)->first();
        if (!$acceptedInternship) {
            return view('end_of_day_reports.weekly_compilation', [
                'noInternship' => true,
            ]);
        }
    
        $startDate = Carbon::parse($acceptedInternship->start_date);
    
        // Decode the schedule and use it based on the student's regular/irregular status
        $schedule = is_array($acceptedInternship->schedule)
            ? $acceptedInternship->schedule
            : json_decode($acceptedInternship->schedule, true);
    
        if ($user->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = is_array($acceptedInternship->custom_schedule)
                ? $acceptedInternship->custom_schedule
                : json_decode($acceptedInternship->custom_schedule, true);
            $scheduleDays = array_keys($customSchedule);
        } else {
            $scheduleDays = array_merge($schedule['days'] ?? [], $schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? []);
        }
    
        // Fetch all reports submitted in the last 7 days
        $reports = EndOfDayReport::where('student_id', $user->id)
            ->whereBetween('submission_for_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->orderBy('submission_for_date', 'asc')
            ->get();


        // Label late submissions
        foreach ($reports as $report) {
            $report->is_late = $report->submission_for_date !== $report->date_submitted->toDateString();
        }


        // Get missing submission dates within this week (last 7 days)
        $missingDates = $this->getMissingWeeklySubmissionDates($user->id, $startOfWeek, $endOfWeek, $scheduleDays);
    
        // Get the student's profile information
        $profile = Auth::user()->profile;
        $studentName = $profile->last_name . ', ' . $profile->first_name;
    
        // Generate the PDF
        $pdf = Pdf::loadView('end_of_day_reports.pdf.weekly_compilation', compact('reports', 'startOfWeek', 'endOfWeek', 'studentName', 'missingDates'));
    
        return $pdf->download("{$studentName}_Weekly_Report.pdf");
    }
    
    
}
