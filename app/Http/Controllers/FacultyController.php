<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Profile;
use App\Models\ActivityLog;
use App\Models\AcademicYear;
use App\Mail\FacultyApprovalMail;
use App\Mail\FacultyUpdateNotificationMail;
use App\Models\AcceptedInternship;
use App\Models\InternshipHours;
use App\Models\Job;
use App\Models\DailyTimeRecord;
use App\Models\EndOfDayReport;
use App\Models\Evaluation;
use App\Models\Application;
use App\Models\EvaluationRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class FacultyController extends Controller
{

    public function dashboard(Request $request)
    {
        $faculty = Auth::user(); // Assuming this is the logged-in faculty member
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Set school year display message
        $schoolYear = $currentAcademicYear ? $currentAcademicYear->start_year . '-' . $currentAcademicYear->end_year : 'Not Set';

        // Only proceed with academic year queries if $currentAcademicYear exists
        $totalStudents = 0;
        $studentFilter = $request->query('student_filter', 'all');

        if ($currentAcademicYear) {
            $studentQuery = User::where('role_id', 5) // Assuming role_id 5 is for students
                ->where('status_id', 1)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->where('course_id', $faculty->course_id); // Filter by faculty's course

            if ($studentFilter !== 'all') {
                $studentQuery->where('course_id', $studentFilter);
            }

            $totalStudents = $studentQuery->count();
        }

        // Count active internships for students in the faculty's course
        $activeInternshipsCount = AcceptedInternship::whereHas('student', function ($query) use ($faculty, $currentAcademicYear) {
            $query->where('course_id', $faculty->course_id)
                ->where('status_id', 1)
                ->where('academic_year_id', $currentAcademicYear->id);
        })->count();

        // Fetch courses with student counts for the academic year, filtered by faculty's course
        $courses = Course::withCount(['students' => function ($query) use ($currentAcademicYear, $faculty) {
            if ($currentAcademicYear) {
                $query->where('academic_year_id', $currentAcademicYear->id)
                    ->where('status_id', 1)
                    ->where('course_id', $faculty->course_id);
            }
        }])->get();

        // Calculate percentages
        $totalPopulation = $totalStudents;
        $studentPercentage = $totalPopulation ? round(($totalStudents / $totalPopulation) * 100, 2) : 0;

        // Set selected course name
        $selectedStudentCourse = $studentFilter === 'all' ? 'All' : Course::find($studentFilter)->course_code;

        // Fetch the list of approved students for faculty's course
        $approvedStudents = User::with('profile', 'course')
            ->where('role_id', 5) // Students
            ->where('status_id', '!=', 3) // Exclude pending students (status_id = 3)
            ->where('course_id', $faculty->course_id) // Only students in faculty's course
            ->get();

        // Attach progress details for each student
        foreach ($approvedStudents as $student) {
            $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();

            if ($acceptedInternship) {
                $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();
                $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
                    ->orderBy('log_date', 'desc')
                    ->first();

                $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();
                $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
                $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : ($internshipHours->hours ?? 0);

                $student->completionPercentage = $remainingHours > 0
                    ? ($totalWorkedHours / $remainingHours) * 100
                    : 100; // If remaining hours are 0, consider the internship completed

                $student->totalWorkedHours = $totalWorkedHours;
                $student->remainingHours = $remainingHours;
                $student->hasInternship = true;

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
                $student->completionPercentage = 0;
                $student->totalWorkedHours = 0;
                $student->remainingHours = 0;
                $student->hasInternship = false;
                $student->applicationStatus = 'No Application';
            }
        }

        $studentsWithNoDTRToday = $this->getStudentsWithNoDTRToday();
        $studentsWithNoEODToday = $this->getStudentsWithNoEODToday();

        // Retrieve pending evaluations
        $pendingEvaluations = $this->listPendingEvaluations();

        return view('faculty.dashboard', compact(
            'totalPopulation',
            'studentPercentage',
            'courses',
            'selectedStudentCourse',
            'schoolYear',
            'approvedStudents',
            'studentsWithNoDTRToday',
            'studentsWithNoEODToday',
            'faculty',
            'activeInternshipsCount',
            'pendingEvaluations'
        ));
    }


    public function getStudentsWithNoDTRToday()
    {
        $today = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('l'); // e.g., 'Monday'
        $todayDate = Carbon::now(new \DateTimeZone('Asia/Manila'))->toDateString(); // Format as 'YYYY-MM-DD'
    
        // Get all active students with accepted internships
        $students = User::where('role_id', 5) // Assuming role_id 5 is for students
                        ->where('status_id', 1) // Assuming status_id 1 is for active students
                        ->whereHas('acceptedInternship')
                        ->with(['acceptedInternship', 'profile', 'course'])
                        ->get();
    
        $studentsWithNoDTRToday = [];
    
        foreach ($students as $student) {
            $acceptedInternship = $student->acceptedInternship;
    
            // Determine schedule based on student type (regular or irregular)
            if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
                $schedule = json_decode($acceptedInternship->custom_schedule, true);
                $scheduledDays = array_keys($schedule); // Days are keys in custom schedules
            } else {
                $schedule = json_decode($acceptedInternship->schedule, true);
                $scheduledDays = $acceptedInternship->work_type === 'Hybrid'
                    ? array_merge($schedule['onsite_days'], $schedule['remote_days'])
                    : $schedule['days'];
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
        $today = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('l'); // e.g., 'Monday'
        $todayDate = Carbon::now(new \DateTimeZone('Asia/Manila'))->toDateString(); // Format as 'YYYY-MM-DD'

        // Get all active students with accepted internships
        $students = User::where('role_id', 5) // Assuming role_id 5 is for students
                        ->where('status_id', 1) // Assuming status_id 1 is for active students
                        ->whereHas('acceptedInternship')
                        ->with(['acceptedInternship', 'profile', 'course'])
                        ->get();

        $studentsWithNoEODToday = [];

        foreach ($students as $student) {
            $acceptedInternship = $student->acceptedInternship;

            // Determine schedule based on student type (regular or irregular)
            if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
                $schedule = json_decode($acceptedInternship->custom_schedule, true);
                $scheduledDays = array_keys($schedule); // Days are keys in custom schedules
            } else {
                $schedule = json_decode($acceptedInternship->schedule, true);
                $scheduledDays = $acceptedInternship->work_type === 'Hybrid'
                    ? array_merge($schedule['onsite_days'], $schedule['remote_days'])
                    : $schedule['days'];
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

    private function listPendingEvaluations()
    {
        $user = auth()->user();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Fetch evaluations that have been sent to the user based on role or specifically to the user
        $evaluations = Evaluation::where('academic_year_id', $currentAcademicYear->id)
            ->whereHas('recipients', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('creator')
            ->get()
            ->filter(function ($evaluation) use ($user) {
                $recipient = EvaluationRecipient::where('evaluation_id', $evaluation->id)
                    ->where('user_id', $user->id)
                    ->first();
                return $recipient && !$recipient->is_answered; // Only include unanswered evaluations
            })
            ->map(function ($evaluation) {
                $evaluation->type_label = $this->getEvaluationTypeLabel($evaluation->evaluation_type);
                return $evaluation;
            });

        return $evaluations;
    }

    // Helper method for evaluation type labels
    private function getEvaluationTypeLabel($type)
    {
        switch ($type) {
            case 'program':
                return 'Program';
            case 'intern_student':
                return 'Intern Performance';
            case 'intern_company':
                return 'Exit Form';
            default:
                return 'Evaluation';
        }
    }


    public function index(Request $request)
    {
        $query = User::where('role_id', 3); // Ensure we're querying only faculty accounts

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }
    
        $faculties = $query->get();
        $courses = Course::all();
    
        return view('faculty.index', compact('faculties', 'courses'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('faculty.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        // Create profile
        $profile = Profile::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);

        // Generate a random password with "aufCCSInternshipFaculty" + 5 random characters
        $password = 'aufCCSInternshipFaculty' . Str::random(5);

        // Create faculty account
        $faculty = User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($password), // Store hashed password
            'role_id' => 3, // Faculty role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
            'course_id' => $request->course_id,
        ]);

        // Log the creation of the faculty account
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Created Faculty',
            'target' => $faculty->profile->first_name . ' ' . $faculty->profile->last_name, // Full name as target
            'changes' => json_encode([
                'name' => $faculty->name,
                'email' => $faculty->email,
                'course' => Course::find($request->course_id)->course_code // Log course code
            ]),
        ]);

        // Send the email with login details
        \Mail::to($faculty->email)->send(new \App\Mail\FacultyApprovalMail(
            $faculty->name,
            $faculty->email,
            $password,
            $faculty->course->course_name
        ));

        return redirect()->route('faculty.index')->with('success', 'Faculty account created successfully.');
    }

    public function show(User $faculty)
    {
        // Ensure we are showing a faculty user (role_id = 3)
        if ($faculty->role_id !== 3) {
            abort(404);
        }

        // Fetch logs for the faculty user
        $logs = ActivityLog::where('admin_id', $faculty->id)->latest()->get();
        
        return view('faculty.show', compact('faculty', 'logs'));
    }

    public function edit(User $faculty)
    {
        $courses = Course::all(); // Get all courses for dropdown
        return view('faculty.edit', compact('faculty', 'courses'));
    }   


    public function update(Request $request, User $faculty)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $faculty->id],
            'id_number' => ['required', 'string', 'max:255', 'unique:profiles,id_number,' . $faculty->profile_id],
            'password' => ['nullable', 'string', 'min:8'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);
    
        $updatedFields = [];
        $newPassword = null;
        $sendEmail = false;

        // Check and log specific changes to profile (first name, last name, id number)
        if ($faculty->profile->first_name != $request->first_name) {
            $updatedFields['First Name'] = ['old' => $faculty->profile->first_name, 'new' => $request->first_name];
        }
        if ($faculty->profile->last_name != $request->last_name) {
            $updatedFields['Last Name'] = ['old' => $faculty->profile->last_name, 'new' => $request->last_name];
        }
        if ($faculty->profile->id_number != $request->id_number) {
            $updatedFields['ID Number'] = ['old' => $faculty->profile->id_number, 'new' => $request->id_number];
        }
    
        // Update profile
        $faculty->profile->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'id_number' => $request->id_number,
        ]);
    
        // Check if course is updated and log the change
        if ($request->course_id != $faculty->course_id) {
            $oldCourse = $faculty->course->course_code;
            $newCourse = Course::find($request->course_id); // Fetch course details
            $updatedFields['Course'] = ['old' => $oldCourse, 'new' => $newCourse->course_code];
            $faculty->course_id = $request->course_id;
        }
    
        // Check if email is updated
        if ($request->email != $faculty->email) {
            $updatedFields['email'] = 'Changed';
            $faculty->email = $request->email;
    
            // Auto-generate a new password if only the email is updated
            $newPassword = 'aufCCSInternshipFaculty' . Str::random(5);
            $faculty->password = Hash::make($newPassword);
            $updatedFields['password'] = 'Generated New';

            $sendEmail = true;
        }
    
        // Check if password is updated
        if ($request->filled('password')) {
            $newPassword = $request->password; // Use the password from the request
            $faculty->password = Hash::make($newPassword);
            $updatedFields['password'] = 'Manually Changed';

            $sendEmail = true;
        }
    
        $faculty->save();

        // Log the update with proper target name and detailed changes
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Updated Faculty',
            'target' => $faculty->profile->first_name . ' ' . $faculty->profile->last_name, // Full name as target
            'changes' => json_encode($updatedFields),
        ]);
    
        // Send email based on changes
        if ($sendEmail) {
            if (isset($updatedFields['email']) && isset($updatedFields['password'])) {
                // Both email and password changed
                \Mail::to($faculty->email)->send(new \App\Mail\FacultyUpdateNotificationMail(
                    $faculty->name,
                    $faculty->email,
                    ['email', 'password'], // Specify both in updated fields
                    $newPassword // Pass the new password
                ));
            } elseif (isset($updatedFields['email'])) {
                // Only email changed
                \Mail::to($faculty->email)->send(new \App\Mail\FacultyUpdateNotificationMail(
                    $faculty->name,
                    $faculty->email,
                    ['email', 'password'], // Pass both email and new password
                    $newPassword
                ));
            } elseif (isset($updatedFields['password'])) {
                // Only password changed
                \Mail::to($faculty->email)->send(new \App\Mail\FacultyUpdateNotificationMail(
                    $faculty->name,
                    $faculty->email,
                    ['password'], // Pass only password
                    $newPassword
                ));
            }
        }
    
        return redirect()->route('faculty.index')->with('success', 'Faculty account updated successfully.');
    }

    public function destroy(User $faculty)
    {
        // Set the faculty's status to inactive instead of deleting
        $faculty->update(['status_id' => 2]); // Status 2 is Inactive

        // Log the deactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Deactivated Faculty',
            'target' => $faculty->profile->first_name . ' ' . $faculty->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Deactivated']),
        ]);

        return redirect()->route('faculty.index')->with('success', 'Faculty account deactivated successfully.');
    }

    public function reactivate(User $faculty)
    {
        // Set the faculty's status to active
        $faculty->update(['status_id' => 1]); // Status 1 is Active
        
        // Log the reactivation
        ActivityLog::create([
            'admin_id' => Auth::id(),
            'action' => 'Reactivated Faculty',
            'target' => $faculty->profile->first_name . ' ' . $faculty->profile->last_name, // Full name as target
            'changes' => json_encode(['status' => 'Reactivated']),
        ]);

        return redirect()->route('faculty.index')->with('success', 'Faculty account reactivated successfully.');
    }
    
}
