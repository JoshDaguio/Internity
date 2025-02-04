<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Profile;
use App\Models\Requirement;
use App\Models\ActivityLog;
use App\Models\AcademicYear;
use App\Models\Priority;
use App\Models\AcceptedInternship;
use App\Models\InternshipHours;
use App\Models\Job;
use App\Models\DailyTimeRecord;
use App\Models\EndOfDayReport;
use App\Models\Evaluation;
use App\Models\EvaluationRecipient;
use App\Models\Application;
use App\Mail\StudentApprovalMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\Storage;
use App\Models\Request as StudReq;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class AdminController extends Controller
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
            ->where('academic_year_id', $currentAcademicYear->id) // Students under the current Academic Year
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

        return view('admin.dashboard', compact(
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
                        ->where('academic_year_id', $currentAcademicYear->id) // Students under the current Academic Year
                        ->get();
    
        $studentsWithNoDTRToday = [];
    
        foreach ($students as $student) {
            $acceptedInternship = $student->acceptedInternship;
    
            // Determine schedule based on student type (regular or irregular)
            if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
                // Handle custom schedule (decode if stored as JSON)
                $schedule = is_string($acceptedInternship->custom_schedule)
                    ? json_decode($acceptedInternship->custom_schedule, true)
                    : $acceptedInternship->custom_schedule;
                
                $scheduledDays = is_array($schedule) ? array_keys($schedule) : [];
            } else {
                // Handle standard schedule (decode if stored as JSON)
                $schedule = is_string($acceptedInternship->schedule)
                    ? json_decode($acceptedInternship->schedule, true)
                    : $acceptedInternship->schedule;
    
                $scheduledDays = is_array($schedule) 
                    ? ($acceptedInternship->work_type === 'Hybrid'
                        ? array_merge($schedule['onsite_days'] ?? [], $schedule['remote_days'] ?? [])
                        : ($schedule['days'] ?? []))
                    : [];
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
                        ->where('academic_year_id', $currentAcademicYear->id) // Students under the current Academic Year
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


    // Method to show pending student registrations
    public function pendingRegistrations(Request $request)
    {
        $user = Auth::user();
    
        $pendingRegistrationsQuery = User::with('profile', 'course')
            ->where('role_id', 5) // Students
            ->where('status_id', 3); // Pending
    
        // Apply Course Filter
        if ($request->has('course_id') && $request->course_id != '') {
            $pendingRegistrationsQuery->where('course_id', $request->course_id);
        }
    
        // If user is Faculty, filter by their course_id
        if ($user->role_id == 3) {
            $pendingRegistrationsQuery->where('course_id', $user->course_id);
        }
    
        $pendingRegistrations = $pendingRegistrationsQuery->get();

        // Fetch all pending registrations for progress bars, regardless of the filter
        $allPendingRegistrations = User::with('profile', 'course')
            ->where('role_id', 5)
            ->where('status_id', 3)
            ->get();
        
        $courses = \App\Models\Course::all();
    
        return view('administrative.pending-registrations', compact('pendingRegistrations', 'allPendingRegistrations', 'courses'));
    }

    // Method to show approved student list
    public function approvedStudents(Request $request)
    {
        $user = Auth::user();


        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        if (!$currentAcademicYear) {
            return view('administrative.student-list', [
                'approvedStudents' => collect([]), // Empty collection
                'courses' => Course::all(),
                'evaluation' => null,
            ])->withErrors(['msg' => 'Current academic year is not set.']);
        }
    
        $query = User::with('profile', 'course', 'requirements')
            ->where('role_id', 5) // Students
            ->where('status_id', '!=', 3)// Exclude pending students (status_id = 3)
            ->where('academic_year_id', $currentAcademicYear->id); // Filter by current academic year

    
        // Apply status filter if provided
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Apply course filter if provided
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Apply requirements filter (completed, to complete, or no submission)
        if ($request->filled('requirements_status')) {
            if ($request->requirements_status == 'complete') {
                $query->whereHas('requirements', function ($q) {
                    $q->whereNotNull('waiver_form')
                    ->whereNotNull('medical_certificate')
                    ->where('status_id', 2); // 'Accepted'
                });
            } elseif ($request->requirements_status == 'incomplete') {
                $query->whereHas('requirements', function ($q) {
                    $q->whereNull('waiver_form')
                    ->orWhereNull('medical_certificate')
                    ->orWhere('status_id', '!=', 2); // Not 'Accepted'
                });
            } elseif ($request->requirements_status == 'no_submission') {
                $query->whereDoesntHave('requirements'); // Students with no submission
            }
        }

        // If user is Faculty, filter by their course_id
        if ($user->role_id == 3) {
            $query->where('course_id', $user->course_id);
        }

        $approvedStudents = $query->get();

        $evaluation = Evaluation::where('evaluation_type', 'intern_company')->first();

        // Apply internship status filter if provided
        if ($request->filled('internship_status')) {
            $approvedStudents = $approvedStudents->filter(function ($student) use ($request) {
                $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();
                $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
                    ->orderBy('log_date', 'desc')
                    ->first();
                $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : null;

                if ($request->internship_status === 'no_internship' && !$acceptedInternship) {
                    return true;
                }
                if ($request->internship_status === 'ongoing' && $acceptedInternship && $remainingHours > 0) {
                    return true;
                }
                if ($request->internship_status === 'complete' && $remainingHours === 0) {
                    return true;
                }
                return false;
            });
        }

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
                $student->internshipHours = $internshipHours->hours;

                // Determine evaluation status based on `EvaluationRecipient`
                if ($remainingHours == 0) { // Internship completed
                    $evaluationSent = EvaluationRecipient::where('evaluation_id', 1) // Replace with actual evaluation ID
                        ->where('user_id', $student->id)
                        ->exists();

                    $student->evaluationStatus = $evaluationSent ? 'Sent' : 'Not Sent';
                } else {
                    $student->evaluationStatus = 'Internship Ongoing';
                }
            
            } else {
                // If no internship is found
                $student->completionPercentage = 0;
                $student->totalWorkedHours = 0;
                $student->remainingHours = 0;
                $student->hasInternship = false;
                $student->evaluationStatus = 'No Internship';
            }
        }
    
        // Get all courses for filtering purposes
        $courses = Course::all();
    
        return view('administrative.student-list', compact('approvedStudents', 'courses', 'evaluation'));
    }

    // Method to approve a student registration
    public function approveRegistration($userId)
    {
        $user = Auth::user();
        $student = User::with('profile', 'course')->findOrFail($userId);

        // Check if Faculty is approving student from their course
        if ($user->role_id == 3 && $student->course_id != $user->course_id) {
            return redirect()->route('registrations.pending')->with('error', 'You can only approve students from your own course.');
        }

        $student->status_id = 1; // Set status to active
        $student->save();

        // Generate a random password (update if password was changed)
        $password = 'aufCCSInternship' . \Str::random(5);

        // Hash the password and update the student user
        $student->update(['password' => Hash::make($password)]);

        // Send approval email
        \Mail::to($student->email)->send(new \App\Mail\StudentApprovalMail(
            $student->name,
            $student->email,
            $password,
            $student->course->course_name
        ));

        // Manually log the approval action
        ActivityLog::create([
            'admin_id' => $user->id,
            'action' => 'Approved Student Registration',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Approved']),
        ]);

        return redirect()->route('registrations.pending')->with('success', 'Student registration approved successfully.');
    }

    // Method to deactivate a student account
    public function deactivateStudent(User $student)
    {
        $student->update(['status_id' => 2]); // Set status to Inactive

        // Log the deactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Deactivated Student',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Deactivated']),
        ]);



        return redirect()->route('students.list')->with('success', 'Student account deactivated successfully.');
    }

    // Method to reactivate a student account
    public function reactivateStudent(User $student)
    {
        $student->update(['status_id' => 1]); // Set status to Active

        // Log the reactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Reactivated Student',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Reactivated']),
        ]);


        return redirect()->route('students.list')->with('success', 'Student account reactivated successfully.');
    }

    // Method to show student details
    public function showStudent(User $student)
    {
        // Fetch the student's priority listings
        $priorityListings = Priority::where('student_id', $student->id)
            ->with('job.company') // Ensure to load the related job and company
            ->orderBy('priority') // Order by the priority level (1st, 2nd, etc.)
            ->get();

        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();

        // Get current date from API
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
            $currentDateTime = Carbon::parse($response->json('datetime'));
        } catch (\Exception $e) {
            $currentDateTime = Carbon::now(new \DateTimeZone('Asia/Manila'));
        }

        if ($acceptedInternship) {
            $startDate = Carbon::parse($acceptedInternship->start_date);
    
            $isIrregular = $student->profile->is_irregular && $acceptedInternship->custom_schedule;

            // Check if student is irregular
            if ($isIrregular) {
                $schedule = is_string($acceptedInternship->custom_schedule)
                    ? json_decode($acceptedInternship->custom_schedule, true)
                    : $acceptedInternship->custom_schedule;
        
                if (!is_array($schedule)) {
                    throw new \Exception("Invalid custom_schedule format");
                }
    
                $scheduledDays = array_keys($schedule); // Extract the days based on keys
            } else {
                // Use regular schedule for regular students
                $schedule = json_decode($acceptedInternship->schedule, true);
    
                // Determine scheduled days based on work type
                if ($acceptedInternship->work_type === 'Hybrid') {
                    // Combine onsite and remote days for hybrid schedules
                    $scheduledDays = array_merge($schedule['onsite_days'], $schedule['remote_days']);
                } else {
                    // For On-site or Remote, use the standard 'days' array
                    $scheduledDays = $schedule['days'];
                }
            }
    
            // Check if today is a scheduled day and after the start date
            $isScheduledDay = in_array($currentDateTime->format('l'), $scheduledDays) && $currentDateTime->gte($startDate);
    
            // Fetch daily records and the latest daily record
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
    
            $student->completionPercentage = $remainingHours > 0 ? ($totalWorkedHours / $remainingHours) * 100 : 100;
            $student->totalWorkedHours = $totalWorkedHours;
            $student->remainingHours = $remainingHours;
            $student->estimatedFinishDate = $this->calculateFinishDate($remainingHours, $startDate, $schedule, $isIrregular);
            $student->hasInternship = true;
            $student->internshipHours = $internshipHours->hours;
        } else {
            // Default values if no internship is found
            $student->completionPercentage = 0;
            $student->totalWorkedHours = 0;
            $student->remainingHours = 0;
            $student->hasInternship = false;
        }

        $evaluation = Evaluation::where('evaluation_type', 'intern_student')
            ->whereHas('recipients', function ($query) use ($student) {
                $query->where('evaluatee_id', $student->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        // Pass the student and priorityListings data to the view
        return view('administrative.show-student', compact('student', 'priorityListings','evaluation'));
    }

    private function calculateFinishDate($remainingHours, $startDate, $schedule, $isIrregular = false)
    {
        $date = Carbon::now();
        $hoursRemaining = $remainingHours;
    
        $dailyWorkHours = [];
    
        if ($isIrregular) {
            // Irregular student: Use custom_schedule for daily hours
            foreach ($schedule as $day => $times) {
                // Validate format for each day's schedule
                if (is_array($times) && isset($times['start'], $times['end'])) {
                    try {
                        $start = Carbon::createFromTimeString($times['start']);
                        $end = Carbon::createFromTimeString($times['end']);
                        $dailyOfficeHours = abs($end->diffInHours($start));
    
                        $dailyHours = $dailyOfficeHours - 1;     
                    } catch (\Exception $e) {
                        throw new \Exception("Invalid time format for day: $day");
                    }
                } else {
                    throw new \Exception("Invalid custom_schedule format for day: $day");
                }
            }
        } else {
            // Regular student: Use standard schedule with start_time and end_time
            if (!isset($schedule['onsite_days'], $schedule['remote_days'], $schedule['start_time'], $schedule['end_time'])) {
                throw new \Exception("Invalid regular schedule format");
            }
    
            $start = Carbon::createFromTimeString($schedule['start_time']);
            $end = Carbon::createFromTimeString($schedule['end_time']);
            $dailyHours = abs($end->diffInHours($start));
    
            // Combine onsite and remote days into one list of working days
            $workingDays = array_merge($schedule['onsite_days'], $schedule['remote_days']);
            $dailyWorkHours = array_fill_keys($workingDays, $dailyHours);
        }
    
        // Loop through days to decrement hoursRemaining
        while ($hoursRemaining > 0) {
            $dayOfWeek = $date->format('l'); // Get the current day of the week
    
            // Only work Monday to Friday
            if (in_array($dayOfWeek, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])) {
                // Deduct 8 hours for the current working day
                $hoursRemaining -= $dailyHours;
            }
    
            // Move to the next day
            $date->addDay();
        }
    
        return $date; // Return the Carbon object
    }

    // Method to show the form for creating a new student
    public function createStudent()
    {
        $courses = Course::all();
        return view('administrative.create-student', compact('courses'));
    }

    // Method to store a new student
    public function storeStudent(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@[auf]+\.(edu\.ph)$/'
            ],
            'id_number' => [
                'required', 
                'string', 
                'max:255', 
                'unique:profiles,id_number',
                'regex:/^\d{2}-\d{4}-\d{3}$/'
            ],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        // Create profile
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Generate a random password
        $password = 'aufCCSInternship' . Str::random(5);

        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Create student account
        $student = User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($password), // Use the generated password
            'role_id' => 5, // Student role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
            'course_id' => $request->course_id,
            'academic_year_id' => $currentAcademicYear->id, // Assign current academic year
        ]);

        // Log the creation of the student
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Created Student',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode([
                'name' => $student->name,
                'email' => $student->email,
                'course_id' => $student->course_id
            ]),
        ]);


        // Send the email with the login details
        \Mail::to($student->email)->send(new \App\Mail\StudentApprovalMail(
            $student->name,
            $student->email,
            $password,
            $student->course->course_name
        ));

        return redirect()->route('students.list')->with('success', 'Student account created successfully.');
    }

    // Method to edit a student account
    public function editStudent(User $student)
    {
        $courses = \App\Models\Course::all();
        return view('administrative.edit-student', compact('student', 'courses'));
    }

    // Method to update a student account
    public function updateStudent(Request $request, User $student)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles,id_number,' . $student->profile_id],
            'password' => ['nullable', 'string', 'min:8'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        $updatedFields = [];
        $newPassword = null;
        $sendEmail = false;

        // Check and log specific changes
        if ($student->profile->first_name != $request->first_name) {
            $updatedFields['First Name'] = ['old' => $student->profile->first_name, 'new' => $request->first_name];
        }
        if ($student->profile->last_name != $request->last_name) {
            $updatedFields['Last Name'] = ['old' => $student->profile->last_name, 'new' => $request->last_name];
        }
        if ($student->profile->id_number != $request->id_number) {
            $updatedFields['ID'] = ['old' => $student->profile->id_number, 'new' => $request->id_number];
        }

        // Update profile
        $student->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Update course if changed and log the change with course code
        if ($request->course_id != $student->course_id) {
            $oldCourse = $student->course->course_code;
            $newCourse = \App\Models\Course::find($request->course_id); // Fetch the course details
            $updatedFields['course'] = ['old' => $student->course->course_code, 'new' => $newCourse->course_code];
            $student->course_id = $request->course_id;
        }

        // Check if email is updated
        if ($request->email != $student->email) {
            $updatedFields['email'] = 'Email Changed';
            $student->email = $request->email;

            // Generate a new password when email changes
            $newPassword = 'aufCCSInternship' . Str::random(5);
            $student->password = Hash::make($newPassword);
            $updatedFields['password'] = 'Generated New';

            // Set flag to send email with new email and password
            $sendEmail = true;
        }

        // Check if password is updated independently
        if ($request->filled('password')) {
            $newPassword = $request->password; // Use the password from the request
            $student->password = Hash::make($newPassword);
            $updatedFields['password'] = 'Manually Changed';

            // Set flag to send email with new password only
            $sendEmail = true;
        }

        // Save the student changes
        $student->save();

        // Log the update with proper target name and detailed changes
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Updated Student',
            'target' => $student->profile->first_name . ' ' . $student->profile->last_name, // Full name as target
            'changes' => json_encode($updatedFields),
        ]);

        // Send email based on changes
        if ($sendEmail) {
            // Send email only if either email or password or both are updated
            if (isset($updatedFields['email']) && isset($updatedFields['password'])) {
                // Both email and password changed
                \Mail::to($student->email)->send(new \App\Mail\StudentUpdateNotificationMail(
                    $student->name,
                    $student->email,
                    ['email', 'password'], // Specify both in updated fields
                    $newPassword // Pass the new password
                ));
            } elseif (isset($updatedFields['email'])) {
                // Only email changed
                \Mail::to($student->email)->send(new \App\Mail\StudentUpdateNotificationMail(
                    $student->name,
                    $student->email,
                    ['email', 'password'], // Pass both email and new password
                    $newPassword
                ));
            } elseif (isset($updatedFields['password'])) {
                // Only password changed
                \Mail::to($student->email)->send(new \App\Mail\StudentUpdateNotificationMail(
                    $student->name,
                    $student->email,
                    ['password'], // Pass only password
                    $newPassword
                ));
            }
        }

        return redirect()->route('students.list')->with('success', 'Student account updated successfully.');
    }


    // Uploading CSV/Excel for Creating students

    // Method to show the upload form
    public function showImportForm()
    {
        return view('administrative.import-students');
    }

    // Method to handle file upload and import students
    public function uploadStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls',
        ]);

        try {
            $import = new StudentsImport();
            Excel::import($import, $request->file('file'));
    
            $skippedCount = $import->getSkippedRows();
    
            $successMessage = 'Students imported successfully.';
            if ($skippedCount > 0) {
                $successMessage .= " Skipped $skippedCount rows, accounts already exist.";
            }
    
            return redirect()->route('students.list')->with('success', $successMessage);
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing students: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filePath = 'public/templates/student_template.xlsx';
        $fileName = 'student_template.xlsx';
    
        if (!Storage::exists($filePath)) {
            abort(404, 'Template file not found.');
        }
    
        return Storage::download($filePath, $fileName);
    }

    //For Irregular Students and Schedule
    public function markIrregular(Request $request, User $student)
    {
        $student->profile->update([
            'is_irregular' => $request->has('is_irregular'),
        ]);

        return redirect()->back()->with('success', 'Student irregular status updated.');
    }

    public function updateSchedule(Request $request, User $student)
    {
        // Validate the submitted schedule contains valid time entries
        $request->validate([
            'schedule.*.start' => 'required|date_format:H:i',
            'schedule.*.end' => 'required|date_format:H:i|after:schedule.*.start',
        ]);
    
        // Retrieve the student's accepted internship
        $acceptedInternship = $student->acceptedInternship;
    
        if ($acceptedInternship) {
            // Save the custom schedule to the accepted internship
            $customSchedule = $request->input('schedule');
    
            // Update the accepted internship with the new custom schedule
            $acceptedInternship->update([
                'custom_schedule' => $customSchedule, // No need to use json_encode, Laravel will handle it
            ]);
    
            return redirect()->back()->with('success', 'Custom schedule updated.');
        }
    
        return redirect()->back()->with('error', 'No accepted internship found.');
    }
    

    
}
