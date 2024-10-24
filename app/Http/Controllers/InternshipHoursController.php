<?php

namespace App\Http\Controllers;

use App\Models\InternshipHours;
use App\Models\Course;
use Illuminate\Http\Request;

class InternshipHoursController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $internshipHours = InternshipHours::with('course')->get();
        return view('internship_hours.index', compact('internshipHours'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courses = Course::all();
        return view('internship_hours.create', compact('courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id|unique:internship_hours,course_id', // Ensure uniqueness
            'hours' => 'required|integer',
        ]);

        InternshipHours::create($request->all());

        return redirect()->route('internship_hours.index')->with('success', 'Internship hour created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $internshipHour = InternshipHours::with('course')->findOrFail($id);
        return view('internship_hours.show', compact('internshipHour'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $internshipHour = InternshipHours::findOrFail($id);
        $courses = Course::all();
        return view('internship_hours.edit', compact('internshipHour', 'courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $internshipHour = InternshipHours::findOrFail($id);

        $request->validate([
            'course_id' => 'required|exists:courses,id|unique:internship_hours,course_id,' . $internshipHour->id,
            'hours' => 'required|integer|min:1',
        ]);

        $internshipHour->update($request->all());

        return redirect()->route('internship_hours.index')->with('success', 'Internship hour updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $internshipHour = InternshipHours::findOrFail($id);
        $internshipHour->delete();

        return redirect()->route('internship_hours.index')->with('success', 'Internship hour deleted successfully.');
    }
}
