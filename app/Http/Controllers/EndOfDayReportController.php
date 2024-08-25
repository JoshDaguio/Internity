<?php

namespace App\Http\Controllers;

use App\Models\EndOfDayReport;
use App\Models\DailyTask;
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
        $query = EndOfDayReport::where('student_id', Auth::id());

        if ($request->has('filter') && $request->filter === 'week') {
            $query->where('date_submitted', '>=', Carbon::now()->subDays(7));
        } elseif ($request->has('filter') && $request->filter === 'month') {
            $query->whereYear('date_submitted', Carbon::now()->year)
                  ->whereMonth('date_submitted', Carbon::now()->month);
        }
    
        $reports = $query->orderBy('date_submitted', 'desc')->get();
    
        return view('end_of_day_reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('end_of_day_reports.create');
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
}
