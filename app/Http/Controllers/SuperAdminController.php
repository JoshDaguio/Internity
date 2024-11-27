<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\AcceptedInternship;
use App\Models\Profile;
use App\Models\Requirement;
use App\Models\ActivityLog;
use App\Models\Job;
use App\Models\Priority;
use App\Models\Application;
use App\Models\InternshipHours;
use App\Models\DailyTimeRecord;
use App\Models\EndOfDayReport;
use App\Models\Evaluation;
use App\Models\EvaluationRecipient;
use App\Models\Request as StudReq;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;



class SuperAdminController extends Controller
{
    public function index(Request $request)
    {
        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $availableJobsCount = Job::where('positions_available', '>', 0)->count();

        $pendingRequests = StudReq::where('status', 'pending')->get();

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

        $query = User::with('profile', 'course')
            ->where('role_id', 5) // Students
            ->where('academic_year_id', $currentAcademicYear->id) // Get Student in the Academic Year
            ->where('status_id', '!=', 3); // Exclude pending students (status_id = 3).

        $approvedStudents = $query->get();

        $evaluation = Evaluation::where('evaluation_type', 'intern_company')->first();

        // Attach progress details for each student
        foreach ($approvedStudents as $student) {
            $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();

            if ($acceptedInternship) {
                // Fetch all daily records and latest daily record
                $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();
                $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
                    ->orderBy('log_date', 'desc')
                    ->first();

                // Fetch total internship hours for the student's course
                $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();

                // Calculate total worked hours
                $totalWorkedHours = $dailyRecords->sum('total_hours_worked');

                // Determine remaining hours based on the latest DTR or fallback to the total internship hours
                $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : ($internshipHours->hours ?? 0);

                if ($remainingHours > 0) {
                    $student->completionPercentage = ($totalWorkedHours / $remainingHours) * 100;
                } else {
                    // If remaining hours are 0, consider the internship completed
                    $student->completionPercentage = 100;
                }

                $student->totalWorkedHours = $totalWorkedHours;
                $student->remainingHours = $remainingHours;
                $student->hasInternship = true;

                // Determine evaluation status based on `EvaluationRecipient`
                if ($remainingHours == 0) { // Internship completed
                    $evaluationSent = EvaluationRecipient::where('evaluation_id', 1) // Replace with actual evaluation ID
                        ->where('user_id', $student->id)
                        ->exists();

                    $student->evaluationStatus = $evaluationSent ? 'Sent' : 'Not Sent';
                } else {
                    $student->evaluationStatus = 'Internship Ongoing';
                }

                $application = Application::where('student_id', $student->id)->latest()->first();
                if ($application) {
                    switch ($application->status->status) {
                        case 'Accepted':
                            $student->applicationStatus = 'Accepted';
                            break;
                        case 'To Review':
                            $student->applicationStatus = 'Pending';
                            break;
                        case 'Rejected':
                            $student->applicationStatus = 'Rejected';
                            break;
                        case 'For Interview':
                            $student->applicationStatus = 'For Interview';
                            break;
                        default:
                            $student->applicationStatus = 'No Application';
                            break;
                    }
                } else {
                    $student->applicationStatus = 'No Application';
                }
            
            } else {
                // If no internship is found
                $student->completionPercentage = 0;
                $student->totalWorkedHours = 0;
                $student->remainingHours = 0;
                $student->hasInternship = false;
                $student->evaluationStatus = 'No Internship';
                $student->applicationStatus = 'No Application';
            }
        }

        $studentsWithNoDTRToday = $this->getStudentsWithNoDTRToday();

        $studentsWithNoEODToday = $this->getStudentsWithNoEODToday();

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
            'totalAcceptedInternships',
            'approvedStudents',
            'evaluation',
            'availableJobsCount',
            'pendingRequests',
            'studentsWithNoDTRToday',
            'studentsWithNoEODToday'
        ));
    }

    public function getStudentsWithNoDTRToday()
    {
        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $today = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('l'); // e.g., 'Monday'
        $todayDate = Carbon::now(new \DateTimeZone('Asia/Manila'))->toDateString(); // Format as 'YYYY-MM-DD'
    
        // Get all active students with accepted internships
        $students = User::where('role_id', 5) // Assuming role_id 5 is for students
                        ->where('status_id', 1) // Assuming status_id 1 is for active students
                        ->whereHas('acceptedInternship')
                        ->with(['acceptedInternship', 'profile', 'course'])
                        ->where('academic_year_id', $currentAcademicYear->id)
                        ->get();
    
        $studentsWithNoDTRToday = [];
    
        foreach ($students as $student) {
            $acceptedInternship = $student->acceptedInternship;
    
            // Determine schedule based on student type (regular or irregular)
            if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
                // Ensure custom_schedule is decoded only if it is a JSON string
                $schedule = is_array($acceptedInternship->custom_schedule) 
                    ? $acceptedInternship->custom_schedule 
                    : json_decode($acceptedInternship->custom_schedule, true);
    
                $scheduledDays = is_array($schedule) ? array_keys($schedule) : []; // Days are keys in custom schedules
            } else {
                // Decode the schedule only if it’s a JSON string
                $schedule = is_array($acceptedInternship->schedule)
                    ? $acceptedInternship->schedule
                    : json_decode($acceptedInternship->schedule, true);
    
                $scheduledDays = is_array($schedule) ? (
                    $acceptedInternship->work_type === 'Hybrid'
                        ? array_merge($schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? [])
                        : ($schedule['days'] ?? [])
                ) : [];
            }
    
            // Check if today is a scheduled day for the student
            if (in_array($today, $scheduledDays)) {
                // Get the latest DTR record to check remaining hours
                $latestDTR = DailyTimeRecord::where('student_id', $student->id)
                                            ->latest('log_date')
                                            ->first();
    
                // Ensure the student has remaining hours greater than 0 and they have not yet completed their internship
                if ($latestDTR && $latestDTR->remaining_hours > 0) {
                    // Check if there is a DTR entry for today with no log times
                    $todayDTR = DailyTimeRecord::where('student_id', $student->id)
                                ->whereDate('log_date', $todayDate)
                                ->first();
    
                    // Add student to the list if there's no DTR for today or if `log_times` is null
                    if (!$todayDTR || is_null($todayDTR->log_times)) {
                        $studentsWithNoDTRToday[] = $student;
                    }
                }
            }
        }
    
        return $studentsWithNoDTRToday;
    }
    
    
    public function getStudentsWithNoEODToday()
    {
        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $today = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('l'); // e.g., 'Monday'
        $todayDate = Carbon::now(new \DateTimeZone('Asia/Manila'))->toDateString(); // Format as 'YYYY-MM-DD'
    
        // Get all active students with accepted internships
        $students = User::where('role_id', 5) // Assuming role_id 5 is for students
                        ->where('status_id', 1) // Assuming status_id 1 is for active students
                        ->whereHas('acceptedInternship')
                        ->with(['acceptedInternship', 'profile', 'course'])
                        ->where('academic_year_id', $currentAcademicYear->id)
                        ->get();
    
        $studentsWithNoEODToday = [];
    
        foreach ($students as $student) {
            $acceptedInternship = $student->acceptedInternship;
    
            // Determine schedule based on student type (regular or irregular)
            if ($student->profile->is_irregular && isset($acceptedInternship->custom_schedule)) {
                $schedule = is_string($acceptedInternship->custom_schedule) ? json_decode($acceptedInternship->custom_schedule, true) : $acceptedInternship->custom_schedule;
                $scheduledDays = array_keys($schedule); // Days are keys in custom schedules
            } else {
                $schedule = is_string($acceptedInternship->schedule) ? json_decode($acceptedInternship->schedule, true) : $acceptedInternship->schedule;
                $scheduledDays = $acceptedInternship->work_type === 'Hybrid'
                    ? array_merge($schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? [])
                    : ($schedule['days'] ?? []);
            }
    
            // Check if today is a scheduled day for the student
            if (in_array($today, $scheduledDays)) {
                // Get the latest DTR record to check remaining hours
                $latestDTR = DailyTimeRecord::where('student_id', $student->id)
                                            ->latest('log_date')
                                            ->first();
    
                // Ensure the student has remaining hours greater than 0 and has not completed their internship
                if ($latestDTR && $latestDTR->remaining_hours > 0) {
                    // Check if there is an EOD report entry for today
                    $eodToday = EndOfDayReport::where('student_id', $student->id)
                                ->whereDate('created_at', $todayDate)
                                ->exists();
    
                    // Add student to the list if there's no EOD report for today
                    if (!$eodToday) {
                        $studentsWithNoEODToday[] = $student;
                    }
                }
            }
        }
    
        return $studentsWithNoEODToday;
    }
    

}
