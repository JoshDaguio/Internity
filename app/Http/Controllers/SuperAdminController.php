<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcceptedInternship;


class SuperAdminController extends Controller
{
    public function index(Request $request)
    {
        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Set school year display message
        $schoolYear = $currentAcademicYear ? $currentAcademicYear->start_year . '-' . $currentAcademicYear->end_year : 'Not Set';

        // Initialize the student and faculty counts
        $totalStudents = 0;
        $totalFaculty = 0;

        // Set up filters for students and faculty
        $studentFilter = $request->query('student_filter', 'all');
        $facultyFilter = $request->query('faculty_filter', 'all');

        // Only proceed with academic year queries if $currentAcademicYear exists
        if ($currentAcademicYear) {
            // Fetch counts for students related to the academic year
            $studentQuery = User::where('role_id', 5) // Assuming role_id 5 is for students
                ->where('status_id', 1)
                ->where('academic_year_id', $currentAcademicYear->id);

            $facultyQuery = User::where('role_id', 3) // Assuming role_id 3 is for faculty
                ->where('status_id', 1);

            // Apply course filter if not 'all'
            if ($studentFilter !== 'all') {
                $studentQuery->where('course_id', $studentFilter);
            }

            if ($facultyFilter !== 'all') {
                $facultyQuery->where('course_id', $facultyFilter);
            }

            $totalStudents = $studentQuery->count();
            $totalFaculty = $facultyQuery->count();
        }

        // Other counts that do not depend on academic year
        $totalAdmins = User::where('role_id', 2)->where('status_id', 1)->count();
        $totalCompanies = User::where('role_id', 4)->where('status_id', 1)->count();
        $totalActiveUsers = User::where('status_id', 1)->count();
        $totalAcceptedInternships = AcceptedInternship::count();

        // Fetch courses with student and faculty counts for the academic year, if set
        $courses = Course::withCount(['students' => function ($query) use ($currentAcademicYear) {
            if ($currentAcademicYear) {
                $query->where('academic_year_id', $currentAcademicYear->id)
                    ->where('status_id', 1);
            }
        }])->withCount(['faculty' => function ($query) {
            $query->where('status_id', 1);
        }])->get();

        // Calculate the total population for percentage calculations
        $totalPopulation = $totalStudents + $totalAdmins + $totalFaculty + $totalCompanies;

        // Calculate percentages
        $adminPercentage = $totalPopulation ? round(($totalAdmins / $totalPopulation) * 100, 2) : 0;
        $facultyPercentage = $totalPopulation ? round(($totalFaculty / $totalPopulation) * 100, 2) : 0;
        $companyPercentage = $totalPopulation ? round(($totalCompanies / $totalPopulation) * 100, 2) : 0;
        $studentPercentage = $totalPopulation ? round(($totalStudents / $totalPopulation) * 100, 2) : 0;

        // Get selected course names for filters
        $selectedStudentCourse = $studentFilter === 'all' ? 'All' : Course::find($studentFilter)->course_code;
        $selectedFacultyCourse = $facultyFilter === 'all' ? 'All' : Course::find($facultyFilter)->course_code;

        if ($request->ajax()) {
            return response()->json([
                'totalStudents' => $totalStudents,
                'studentPercentage' => $studentPercentage,
                'selectedStudentCourse' => $selectedStudentCourse,
                'totalFaculty' => $totalFaculty,
                'facultyPercentage' => $facultyPercentage,
                'selectedFacultyCourse' => $selectedFacultyCourse,
            ]);
        }

        return view('super_admin.dashboard', compact(
            'totalAdmins',
            'adminPercentage',
            'totalFaculty',
            'facultyPercentage',
            'totalCompanies',
            'companyPercentage',
            'totalStudents',
            'studentPercentage',
            'courses',
            'selectedStudentCourse',
            'selectedFacultyCourse',
            'schoolYear',
            'totalActiveUsers',
            'totalAcceptedInternships'
        ));
    }

}
