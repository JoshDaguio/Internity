<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use App\Models\PenaltiesAwarded;
use App\Models\User;
use App\Models\DailyTimeRecord;
use App\Models\AcceptedInternship;
use App\Models\InternshipHours;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenaltyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penalties = Penalty::all();
        return view('penalties.index', compact('penalties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('penalties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'violation' => 'required|string|max:255',
            'penalty_type' => 'required|in:fixed,conditional',
            'penalty_hours' => 'required_if:penalty_type,fixed|nullable|integer|min:1',
            'conditions' => 'required_if:penalty_type,conditional|nullable|string|max:255',
        ]);

        Penalty::create($request->all());

        return redirect()->route('penalties.index')->with('success', 'Penalty created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $penalty = Penalty::findOrFail($id);
        return view('penalties.show', compact('penalty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penalty = Penalty::findOrFail($id);
        return view('penalties.edit', compact('penalty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'violation' => 'required|string|max:255',
            'penalty_type' => 'required|in:fixed,conditional',
            'penalty_hours' => 'required_if:penalty_type,fixed|nullable|integer|min:1',
            'conditions' => 'required_if:penalty_type,conditional|nullable|string|max:255',
        ]);
        
        $penalty = Penalty::findOrFail($id);
        $penalty->update($request->all());

        return redirect()->route('penalties.index')->with('success', 'Penalty updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penalty = Penalty::findOrFail($id);
        $penalty->delete();

        return redirect()->route('penalties.index')->with('success', 'Penalty deleted successfully.');
    }

    // Awarding of Penalty
    public function awardPenalty(Request $request, $studentId)
    {
        $student = User::findOrFail($studentId);
        $penalty = Penalty::findOrFail($request->penalty_id);

        // Fetch the latest DTR for today
        $dailyRecord = DailyTimeRecord::where('student_id', $student->id)
            ->latest('log_date')
            ->first();

        
        if (!$dailyRecord) {
            return redirect()->back()->with('error', 'No DTR record found for the student.');
        }
        

        // Calculate penalty hours
        $penaltyHours = $penalty->penalty_hours;
        if ($penalty->penalty_type === 'conditional' && $penalty->conditions) {
            // Add custom logic here for calculating penalty hours for conditional penalties
            // For example, for tardiness, calculate based on the condition
        }

        // Add penalty to the student's remaining hours
        $totalWorkedHours = DailyTimeRecord::where('student_id', $student->id)->sum('total_hours_worked');
        $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();
        $remainingHours = $internshipHours->hours - $totalWorkedHours + $penaltyHours;

        // Award the penalty
        PenaltiesAwarded::create([
            'student_id' => $studentId,
            'penalty_id' => $penalty->id,
            'dtr_id' => $dailyRecord->id,
            'penalty_hours' => $penaltyHours,
            'awarded_date' => Carbon::now(),
            'remarks' => $request->remarks,
        ]);

        // Update remaining hours for the student in the latest DTR
        $dailyRecord->remaining_hours = $remainingHours;
        $dailyRecord->save();

        return redirect()->back()->with('success', 'Penalty awarded successfully.');
    }

}
