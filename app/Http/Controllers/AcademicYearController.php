<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\User;
use App\Models\AccountStatus;
use App\Models\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    // Show Academic Year Configuration Page
    public function index()
    {
        $academicYears = AcademicYear::all();
        return view('academic_years.index', compact('academicYears'));
    }

    // Show the form for creating a new academic year
    public function create()
    {
        // Generate an array of years for the dropdown (e.g., 2000-2050)
        $years = range(date('Y'), date('Y') + 10); // Next 10 years
        return view('academic_years.create', compact('years'));
    }

    // Store a new academic year
    public function store(Request $request)
    {
        $request->validate([
            'start_year' => 'required|digits:4',
            'end_year' => 'required|digits:4|gt:start_year',
        ]);

        AcademicYear::create([
            'start_year' => $request->start_year,
            'end_year' => $request->end_year,
        ]);

        return redirect()->route('academic-years.index')->with('success', 'Academic Year added successfully.');
    }

    public function show($id, Request $request)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $courses = Course::all();

        // Query for all students in the selected academic year
        $query = User::where('academic_year_id', $id)->with('course');

        // Filter students by the selected course if available
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $students = $query->get();

        // Count students per course in the selected academic year
        $studentsPerCourse = User::where('academic_year_id', $id)
            ->select('course_id', \DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->with('course')
            ->get();

        return view('academic_years.show', compact('academicYear', 'students', 'courses', 'studentsPerCourse'));
    }


    // Show the form for editing an academic year
    public function edit($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $years = range(date('Y'), date('Y') + 10); // Next 10 years
        return view('academic_years.edit', compact('academicYear', 'years'));
    }

    // Update an existing academic year
    public function update(Request $request, $id)
    {
        $request->validate([
            'start_year' => 'required|digits:4',
            'end_year' => 'required|digits:4|gt:start_year',
        ]);

        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update([
            'start_year' => $request->start_year,
            'end_year' => $request->end_year,
        ]);

        return redirect()->route('academic-years.index')->with('success', 'Academic Year updated successfully.');
    }

    // Set the current academic year
    public function setCurrent($id)
    {
        // Find the currently active academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        if ($currentAcademicYear) {
            // Deactivate the current academic year
            $currentAcademicYear->update(['is_current' => false]);

            // Deactivate all student accounts associated with this academic year
            User::where('academic_year_id', $currentAcademicYear->id)
                ->update(['status_id' => 2]); // Status 2 = Inactive
        }

        // Set the selected academic year as current
        $newAcademicYear = AcademicYear::findOrFail($id);
        $newAcademicYear->update(['is_current' => true]);

        // Reactivate student accounts associated with the new current academic year
        User::where('academic_year_id', $newAcademicYear->id)
            ->update(['status_id' => 1]); // Status 1 = Active

        return redirect()->back()->with('success', 'Current academic year set successfully.');
    }

    // Deactivate an academic year
    public function deactivate($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $academicYear->update(['is_current' => false]);

        // Set all users associated with this academic year to inactive
        User::where('academic_year_id', $academicYear->id)
            ->update(['status_id' => 2]); // Status 2 = Inactive

        return redirect()->back()->with('success', 'Academic Year deactivated successfully. All associated student accounts have also been deactivated.');
    }

}
