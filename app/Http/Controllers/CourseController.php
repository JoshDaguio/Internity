<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Profile;
use App\Models\ActivityLog;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Only count students and faculty who are Active (status_id 1) or Inactive (status_id 2)
        $courses = Course::withCount([
            'students' => function ($query) {
                $query->whereIn('status_id', [1, 2]); // Only Active and Inactive students
            },
            'faculty' => function ($query) {
                $query->whereIn('status_id', [1, 2]); // Only Active and Inactive faculty
            }
        ])->get();

        // Prepare data for the pie chart (total population per course)
        $coursePopulationData = $courses->map(function ($course) {
            return [
                'course' => $course->course_code,
                'population' => $course->faculty_count + $course->students_count, // Use the filtered count
            ];
        });

        return view('courses.index', compact('courses', 'coursePopulationData'));
    }

    public function getAllCourses()
    {
        // Fetch only the course id and course_code for populating the dropdown
        $courses = Course::all(['id', 'course_code']);
        return response()->json($courses);
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
            'course_code' => 'required|string|max:255|unique:courses,course_code',
            'course_name' => 'required|string|max:255',
        ]);

        try {
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
    
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['course_code' => 'The course code already exists. Please use a different course code.']);
        }    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
    
        if (!$currentAcademicYear) {
            return view('courses.show', [
                'course' => Course::findOrFail($id),
                'students' => collect([]), // Empty collection
                'studentsChartData' => [],
                'facultyChartData' => [],
            ])->withErrors(['msg' => 'Current academic year is not set. No students available.']);
        }
    
        // Fetch the course with filtered student and faculty counts
        $course = Course::withCount([
            'students' => function ($query) use ($currentAcademicYear) {
                $query->whereIn('status_id', [1, 2]) // Active and Inactive
                      ->where('academic_year_id', $currentAcademicYear->id); // Current academic year
            },
            'faculty' => function ($query) {
                $query->whereIn('status_id', [1, 2]); // Active and Inactive
            }
        ])->findOrFail($id);
    
        // Fetch students for the course in the current academic year
        $students = User::where('role_id', 5) // Role ID 5 for students
            ->whereIn('status_id', [1, 2]) // Active and Inactive students
            ->where('academic_year_id', $currentAcademicYear->id) // Current academic year
            ->where('course_id', $course->id)
            ->with('profile')
            ->get();
    
        // Total students with Active and Inactive status
        $totalStudents = User::where('role_id', 5)
            ->whereIn('status_id', [1, 2])
            ->where('academic_year_id', $currentAcademicYear->id)
            ->count();
    
        $studentsChartData = [
            ['name' => $course->course_code, 'value' => $students->count()],
            ['name' => 'Other Courses', 'value' => $totalStudents - $students->count()],
        ];
    
        // Total faculty with Active and Inactive status
        $totalFaculty = User::where('role_id', 3) // Role ID 3 for faculty
            ->whereIn('status_id', [1, 2]) // Active and Inactive
            ->count();
    
        $facultyChartData = [
            ['name' => $course->course_code, 'value' => $course->faculty_count],
            ['name' => 'Other Courses', 'value' => $totalFaculty - $course->faculty_count],
        ];
    
        return view('courses.show', compact('course', 'students', 'studentsChartData', 'facultyChartData'));
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
