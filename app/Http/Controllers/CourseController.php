<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Profile;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::withCount(['faculty', 'students'])->get();

        // Prepare data for the pie chart (total population per course)
        $coursePopulationData = $courses->map(function ($course) {
            return [
                'course' => $course->course_code,
                'population' => $course->faculty_count + $course->students_count,
            ];
        });

        return view('courses.index', compact('courses', 'coursePopulationData'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_code' => 'required|string|max:255',
            'course_name' => 'required|string|max:255',
        ]);

        $course = Course::create($request->all());

        // Log the creation of the course
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Created Course',
            'target' => $course->course_code . ' - ' . $course->course_name,
            'changes' => json_encode([
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
            ]),
        ]);

        return redirect()->route('courses.index')->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $course = Course::withCount(['students', 'faculty'])->findOrFail($id);

        // Prepare data for students chart
        $totalStudents = User::where('role_id', 5)->count(); // Assuming role_id 5 is for students
        $studentsChartData = [
            ['name' => $course->course_code, 'value' => $course->students->count()],
            ['name' => 'Other Courses', 'value' => $totalStudents - $course->students->count()],
        ];

        // Prepare data for faculty chart
        $totalFaculty = User::where('role_id', 3)->count(); // Assuming role_id 3 is for faculty
        $facultyChartData = [
            ['name' => $course->course_code, 'value' => $course->faculty->count()],
            ['name' => 'Other Courses', 'value' => $totalFaculty - $course->faculty->count()],
        ];

        return view('courses.show', compact('course', 'studentsChartData', 'facultyChartData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $course = Course::findOrFail($id);
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'course_code' => 'required|string|max:255',
            'course_name' => 'required|string|max:255',
        ]);

        $course = Course::findOrFail($id);
            
        // Capture the old values before updating
        $oldCourseCode = $course->course_code;
        $oldCourseName = $course->course_name;

        // Update the course
        $course->update($request->all());

        // Log the changes if any
        $updatedFields = [];
        if ($oldCourseCode != $course->course_code) {
            $updatedFields['Course Code'] = ['old' => $oldCourseCode, 'new' => $course->course_code];
        }
        if ($oldCourseName != $course->course_name) {
            $updatedFields['Course Name'] = ['old' => $oldCourseName, 'new' => $course->course_name];
        }

        $course->update($request->all());


        if (!empty($updatedFields)) {
            ActivityLog::create([
                'admin_id' => Auth::id(),
                'action' => 'Updated Course',
                'target' => $course->course_code,
                'changes' => json_encode($updatedFields),
            ]);
        }

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }
}
