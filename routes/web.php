<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\InternshipHoursController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\AdminAccountController;
use App\Http\Controllers\EndOfDayReportController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\SkillTagController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AdminJobController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DailyTimeRecordController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\PulloutController;
use App\Http\Controllers\InternshipFilesController;
use App\Http\Controllers\RequestController;




Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $role = Auth::user()->role_id ?? null;

    // Redirect based on user role
    switch ($role) {
        case 1:
            return redirect()->route('super_admin.dashboard');
        case 2:
            return redirect()->route('admin.dashboard');
        case 3:
            return redirect()->route('faculty.dashboard');
        case 4:
            return redirect()->route('company.dashboard');
        case 5:
            return redirect()->route('student.dashboard');
        default:
            return abort(403, 'Unauthorized access');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

//Forget Password / Password Reset
Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

//Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile.profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/previewCV/{id}', [ProfileController::class, 'previewCV'])->name('profile.previewCV');
});

require __DIR__.'/auth.php';

// route::get('super_admin/dashboard',[SuperAdminController::class,'index'])->
//     middleware(['auth','super_admin']);
// route::get('admin/dashboard',[AdminController::class,'index'])->
//     middleware(['auth','admin']);
// route::get('student/dashboard',[StudentController::class,'index'])->
//     middleware(['auth','student']);

//Dashboard
route::get('/super_admin/dashboard', [SuperAdminController::class, 'index'])->middleware(['auth','super_admin'])->name('super_admin.dashboard');
route::get('/admin/dashboard', [AdminController::class, 'index'])->middleware(['auth','admin'])->name('admin.dashboard');
route::get('/faculty/dashboard', [FacultyController::class, 'dashboard'])->middleware(['auth','faculty'])->name('faculty.dashboard');
route::get('/company/dashboard', [CompanyController::class, 'dashboard'])->middleware(['auth','company'])->name('company.dashboard');
route::get('/student/dashboard', [StudentController::class, 'index'])->middleware(['auth','student'])->name('student.dashboard');

//Course CRUD. Only Super Admin/Admin
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::patch('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
});

//Internship Hours CRUD. Only Super Admin/Admin
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/internship_hours', [InternshipHoursController::class, 'index'])->name('internship_hours.index');
    Route::get('/internship_hours/create', [InternshipHoursController::class, 'create'])->name('internship_hours.create');
    Route::post('/internship_hours', [InternshipHoursController::class, 'store'])->name('internship_hours.store');
    Route::get('/internship_hours/{internship_hours}', [InternshipHoursController::class, 'show'])->name('internship_hours.show');
    Route::get('/internship_hours/{internship_hours}/edit', [InternshipHoursController::class, 'edit'])->name('internship_hours.edit');
    Route::patch('/internship_hours/{internship_hours}', [InternshipHoursController::class, 'update'])->name('internship_hours.update');
    Route::delete('/internship_hours/{internship_hours}', [InternshipHoursController::class, 'destroy'])->name('internship_hours.destroy');
});


//Penalty CRUD. Only Super Admin/Admin
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/penalties', [PenaltyController::class, 'index'])->name('penalties.index');
    Route::get('/penalties/create', [PenaltyController::class, 'create'])->name('penalties.create');
    Route::post('/penalties', [PenaltyController::class, 'store'])->name('penalties.store');
    Route::get('/penalties/{penalty}', [PenaltyController::class, 'show'])->name('penalties.show');
    Route::get('/penalties/{penalty}/edit', [PenaltyController::class, 'edit'])->name('penalties.edit');
    Route::patch('/penalties/{penalty}', [PenaltyController::class, 'update'])->name('penalties.update');
    Route::delete('/penalties/{penalty}', [PenaltyController::class, 'destroy'])->name('penalties.destroy');
    Route::post('/students/{studentId}/penalties', [PenaltyController::class, 'awardPenalty'])->name('penalties.award');
});

//Jobs CRUD. Only Super Admin/Admin and Company
Route::middleware(['auth', 'job_access'])->group(function () {
    // Creating of Jobs
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
    Route::patch('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');

    //  Accepting of Interns
    // Intern Applications Page
    Route::get('/company/intern-applications', [CompanyController::class, 'internApplications'])->name('company.internApplications');
    // Job Applications Page
    Route::get('/company/job-applications/{job}', [CompanyController::class, 'jobApplications'])->name('company.jobApplications');

    // Route for updating application status
    Route::patch('/company/application/{application}/status/{status}', [CompanyController::class, 'changeStatus'])->name('application.updateStatus');

    // Route for scheduling an interview
    Route::post('/company/application/{application}/schedule-interview', [CompanyController::class, 'scheduleInterview'])->name('application.scheduleInterview');

    Route::get('/company/interns', [CompanyController::class, 'interns'])->name('company.interns');
});

// Faculty CRUD. Only Super Admin/Admin
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/faculty', [FacultyController::class, 'index'])->name('faculty.index');
    Route::get('/faculty/create', [FacultyController::class, 'create'])->name('faculty.create');
    Route::post('/faculty', [FacultyController::class, 'store'])->name('faculty.store');
    Route::get('/faculty/{faculty}', [FacultyController::class, 'show'])->name('faculty.show');
    Route::get('/faculty/{faculty}/edit', [FacultyController::class, 'edit'])->name('faculty.edit');
    Route::patch('/faculty/{faculty}', [FacultyController::class, 'update'])->name('faculty.update');
    Route::delete('/faculty/{faculty}', [FacultyController::class, 'destroy'])->name('faculty.destroy');
    Route::patch('/faculty/{faculty}/reactivate', [FacultyController::class, 'reactivate'])->name('faculty.reactivate');
});

// Company CRUD. Only Super Admin/Admin
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
    Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
    Route::get('/company/import', [CompanyController::class, 'showImportForm'])->name('company.import');
    Route::post('/company/upload', [CompanyController::class, 'uploadCompanies'])->name('company.upload');
    Route::get('/company/template', [CompanyController::class, 'downloadTemplate'])->name('company.template');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/company/{company}', [CompanyController::class, 'show'])->name('company.show');
    Route::get('/company/{company}/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::patch('/company/{company}', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('/company/{company}', [CompanyController::class, 'destroy'])->name('company.destroy');
    Route::patch('/company/{company}/reactivate', [CompanyController::class, 'reactivate'])->name('company.reactivate');
});

//Student Registration Process
Route::middleware(['auth', 'facultyaccess'])->group(function () {
    Route::get('/registrations/pending', [AdminController::class, 'pendingRegistrations'])->name('registrations.pending');
    Route::post('/registrations/approve/{user}', [AdminController::class, 'approveRegistration'])->name('registrations.approve');
    Route::get('/students/list', [AdminController::class, 'approvedStudents'])->name('students.list');

    Route::get('/students/create', [AdminController::class, 'createStudent'])->name('students.create');
    Route::post('/students/store', [AdminController::class, 'storeStudent'])->name('students.store');

    Route::get('/students/{student}/edit', [AdminController::class, 'editStudent'])->name('students.edit');
    Route::patch('/students/{student}', [AdminController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{student}/deactivate', [AdminController::class, 'deactivateStudent'])->name('students.deactivate');
    Route::patch('/students/{student}/reactivate', [AdminController::class, 'reactivateStudent'])->name('students.reactivate');

    // Route to download the template file
    Route::get('/students/template', [AdminController::class, 'downloadTemplate'])->name('students.template');

    // Route to show the upload form
    Route::get('/students/import', [AdminController::class, 'showImportForm'])->name('students.import');

    // Route to handle the file upload and import process
    Route::post('/students/upload', [AdminController::class, 'uploadStudents'])->name('students.upload');
});

//For Irregular Students
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::patch('/students/{student}/irregular', [AdminController::class, 'markIrregular'])->name('students.markIrregular');
    Route::patch('/students/{student}/schedule', [AdminController::class, 'updateSchedule'])->name('students.updateSchedule');
});

//Registration Success - Currently not working
Route::get('/register/success', function () {
    return view('auth.register-success');
})->name('register.success');

// Promotion and Demotion of Account Roles (Admin, Faculty). Only Super Admin
Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::patch('/admin-accounts/{admin}/promote', [AdminAccountController::class, 'promote'])->name('admin-accounts.promote');
    Route::patch('/admin-accounts/{admin}/demote', [AdminAccountController::class, 'demote'])->name('admin-accounts.demote');
});


//Admin Account CRUD. Only Super Admin
Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/admin-accounts', [AdminAccountController::class, 'index'])->name('admin-accounts.index');
    Route::get('/admin-accounts/create', [AdminAccountController::class, 'create'])->name('admin-accounts.create');
    Route::post('/admin-accounts', [AdminAccountController::class, 'store'])->name('admin-accounts.store');
    Route::get('/admin-accounts/{admin}/edit', [AdminAccountController::class, 'edit'])->name('admin-accounts.edit');
    Route::patch('/admin-accounts/{admin}', [AdminAccountController::class, 'update'])->name('admin-accounts.update');
    Route::delete('/admin-accounts/{admin}', [AdminAccountController::class, 'destroy'])->name('admin-accounts.destroy');
    Route::patch('/admin-accounts/{admin}/reactivate', [AdminAccountController::class, 'reactivate'])->name('admin-accounts.reactivate');
    Route::get('/admin-accounts/{admin}', [AdminAccountController::class, 'show'])->name('admin-accounts.show');
});


//EOD Reports CRUD for all roles with their respective access rights
Route::middleware(['auth'])->group(function () {
    //Students Create and View Reports
    Route::get('/end_of_day_reports', [EndOfDayReportController::class, 'index'])->name('end_of_day_reports.index');
    Route::get('/end_of_day_reports/create', [EndOfDayReportController::class, 'create'])->name('end_of_day_reports.create');
    Route::post('/end_of_day_reports', [EndOfDayReportController::class, 'store'])->name('end_of_day_reports.store');
    
    //Show specific report for the user (student, super admin, admin, faculty)
    Route::get('/end_of_day_reports/{report}', [EndOfDayReportController::class, 'show'])->name('end_of_day_reports.show');

    //Generate and View Monthly Compilation for Students
    Route::get('/end_of_day_reports/compile/monthly', [EndOfDayReportController::class, 'compileMonthly'])->name('end_of_day_reports.compile.monthly');
    Route::get('/end_of_day_reports/download/monthly', [EndOfDayReportController::class, 'downloadMonthlyPDF'])->name('end_of_day_reports.download.monthly');

    // Generate and View Weekly Compilation for Students
    Route::get('/end_of_day_reports/compile/weekly', [EndOfDayReportController::class, 'compileWeekly'])->name('end_of_day_reports.compile.weekly');
    Route::get('/end_of_day_reports/download/weekly', [EndOfDayReportController::class, 'downloadWeeklyPDF'])->name('end_of_day_reports.download.weekly');
});


//File Uploads and Downloads
//Super Admins and Admins Can Upload files
Route::middleware(['auth', 'facultyaccess'])->group(function () {
    Route::get('/file_uploads', [FileUploadController::class, 'index'])->name('file_uploads.index');
    Route::get('/file_uploads/create', [FileUploadController::class, 'create'])->name('file_uploads.create');
    Route::post('/file_uploads', [FileUploadController::class, 'store'])->name('file_uploads.store');
    Route::delete('/file_uploads/{file}', [FileUploadController::class, 'destroy'])->name('file_uploads.destroy');
    Route::get('/file_uploads/preview/{file}', [FileUploadController::class, 'preview'])->name('file_uploads.preview');
    Route::get('/file_uploads/{file}/edit', [FileUploadController::class, 'edit'])->name('file_uploads.edit'); // Add edit route
    Route::patch('/file_uploads/{file}', [FileUploadController::class, 'update'])->name('file_uploads.update'); // Add update route
    Route::post('/file_uploads/restore/{id}', [FileUploadController::class, 'restore'])->name('file_uploads.restore');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/file_uploads', [FileUploadController::class, 'index'])->name('file_uploads.index');
    Route::get('/file_uploads/download/{file}', [FileUploadController::class, 'download'])->name('file_uploads.download');
    Route::get('/file_uploads/preview/{file}', [FileUploadController::class, 'preview'])->name('file_uploads.preview');
});

//Skill Tags CRUD
Route::middleware(['auth'])->group(function () {
    Route::resource('skill_tags', SkillTagController::class);
});

// Student Job Application
Route::middleware('auth')->group(function () {
    Route::get('/internship/listings', [StudentController::class, 'internshipListings'])->name('internship.listings');
    Route::post('/internship/priority', [StudentController::class, 'setPriority'])->name('internship.priority');
    Route::get('/internship/applications', [StudentController::class, 'internshipApplications'])->name('internship.applications');
    Route::post('/internship/submit/{jobId}', [StudentController::class, 'submitApplication'])->name('internship.submit');
    Route::get('/application/preview/{type}/{id}', [StudentController::class, 'previewFile'])->name('application.preview');
    Route::post('/internship/remove-priority/{jobId}', [StudentController::class, 'removePriority'])->name('internship.removePriority');
});

// Priority Management for Admins
// Admin Routes for Managing Student Priorities
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/admin/students/{studentId}/manage-priority', [StudentController::class, 'showManagePriorityPage'])->name('admin.students.managePriority'); // New GET route
    Route::post('/admin/students/{studentId}/set-priority', [StudentController::class, 'adminSetPriority'])->name('admin.students.setPriority');
    Route::post('/admin/students/{studentId}/remove-priority/{jobId}', [StudentController::class, 'adminRemovePriority'])->name('admin.students.removePriority');
});


//Requirements
//Student Side
Route::middleware(['auth'])->group(function () {
    Route::get('/requirements', [RequirementController::class, 'index'])->name('requirements.index');
    Route::post('/requirements/submit', [RequirementController::class, 'submit'])->name('requirements.submit');

    // Specific routes for each requirement
    Route::post('/requirements/submit-waiver', [RequirementController::class, 'submitWaiver'])->name('submit.waiver');
    Route::post('/requirements/submit-medical', [RequirementController::class, 'submitMedical'])->name('submit.medical');
    // Route::post('/requirements/submit-consent', [RequirementController::class, 'submitConsent'])->name('submit.consent');
    Route::get('/requirements/preview/{type}/{id}', [RequirementController::class, 'previewFile'])->name('preview.requirement');
    Route::get('/requirements/download/{type}/{id}', [RequirementController::class, 'downloadFile'])->name('download.requirement');

});

// Admin Side
Route::middleware(['auth', 'administrative'])->group(function () {
    // Admin Requirement Review
    Route::get('/requirements/review/{requirement}', [RequirementController::class, 'review'])->name('requirements.review');
    // Route::get('/requirements/review/{studentId}', [RequirementController::class, 'review'])->name('requirements.review');
    Route::post('/requirements/accept-waiver/{id}', [RequirementController::class, 'acceptWaiver'])->name('admin.accept.waiver');
    Route::post('/requirements/reject-waiver/{id}', [RequirementController::class, 'rejectWaiver'])->name('admin.reject.waiver');
    Route::post('/requirements/accept-medical/{id}', [RequirementController::class, 'acceptMedical'])->name('admin.accept.medical');
    Route::post('/requirements/reject-medical/{id}', [RequirementController::class, 'rejectMedical'])->name('admin.reject.medical');
    Route::post('/requirements/upload-endorsement/{id}', [RequirementController::class, 'uploadEndorsement'])->name('admin.upload.endorsement');
    Route::get('/requirements/download/{type}/{id}', [RequirementController::class, 'downloadFile'])->name('download.requirement');
});

// Academic Year Management Routes for Super Admin
Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::get('/academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
    Route::patch('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::post('/academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');
    Route::post('/academic-years/{academicYear}/deactivate', [AcademicYearController::class, 'deactivate'])->name('academic-years.deactivate');
    Route::get('/academic-years/{id}/show', [AcademicYearController::class, 'show'])->name('academic-years.show');
});

// Job Listing CRUD for Super Admins and Admins
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/admin/jobs', [AdminJobController::class, 'index'])->name('admin.jobs.index');
    Route::get('/admin/jobs/create', [AdminJobController::class, 'create'])->name('admin.jobs.create');
    Route::post('/admin/jobs', [AdminJobController::class, 'store'])->name('admin.jobs.store');
    Route::get('/admin/jobs/{job}/edit', [AdminJobController::class, 'edit'])->name('admin.jobs.edit');
    Route::patch('/admin/jobs/{job}', [AdminJobController::class, 'update'])->name('admin.jobs.update');
    Route::delete('/admin/jobs/{job}', [AdminJobController::class, 'destroy'])->name('admin.jobs.destroy');
});

// Messages
// Routes for the message/inbox system
Route::middleware(['auth'])->group(function () {
    Route::get('/api/courses', [CourseController::class, 'getAllCourses'])->name('api.courses.index');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/compose', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/recipients/{role}', [MessageController::class, 'getRecipients'])->name('messages.recipients');
    Route::post('/messages/reply/{id}', [MessageController::class, 'reply'])->name('messages.reply');
});

// Daily Time Record
Route::middleware(['auth'])->group(function () {
    Route::get('/daily-time-records', [DailyTimeRecordController::class, 'index'])->name('dtr.index');
    Route::post('/daily-time-records/log/{type}', [DailyTimeRecordController::class, 'logTime'])->name('dtr.logTime');
    Route::get('/reports', [DailyTimeRecordController::class, 'reports'])->name('dtr.reports');
    Route::post('/reports/generate-pdf', [DailyTimeRecordController::class, 'generateReportPDF'])->name('reports.generate_pdf');
});

Route::middleware(['auth', 'studentmonitoring'])->group(function () {
    Route::get('/students/{student}/dtr', [DailyTimeRecordController::class, 'studentDTR'])->name('students.dtr');
    Route::get('/students/{student}/eod', [EndOfDayReportController::class, 'studentEOD'])->name('students.eod');
    Route::get('/students/{student}/show', [AdminController::class, 'showStudent'])->name('students.show');
});


// Evaluations Admins Access
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/evaluations', [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/create', [EvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/evaluations/store', [EvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/evaluations/{evaluation}/results', [EvaluationController::class, 'viewResults'])->name('evaluations.results');
    Route::post('/evaluations/{evaluation}/submit', [EvaluationController::class, 'storeResponse'])->name('evaluations.submitResponse');  
    Route::get('/evaluations/{evaluation}/submit', [EvaluationController::class, 'showResponseForm'])->name('evaluations.showResponseForm');
    Route::get('/evaluations/{evaluation}/downloadPDF', [EvaluationController::class, 'downloadPDF'])->name('evaluations.downloadPDF');
    Route::get('/evaluations/{evaluation}/download-excel', [EvaluationController::class, 'downloadExcel'])->name('evaluations.downloadExcel');
    Route::post('/evaluations/{evaluation}/send', [EvaluationController::class, 'sendEvaluation'])->name('evaluations.send');
    Route::get('/evaluations/{evaluation}/view-response', [EvaluationController::class, 'viewUserResponse'])->name('evaluations.viewUserResponse');
    Route::get('/evaluations/{evaluation}/downloadResponsePDF', [EvaluationController::class, 'downloadResponsePDF'])->name('evaluations.downloadResponsePDF');
    Route::get('/evaluations/intern_student/{evaluation}/recipients', [EvaluationController::class, 'internStudentRecipientList'])->name('evaluations.internStudentRecipientList');
    Route::get('/evaluations/intern_company/{evaluation}/recipients', [EvaluationController::class, 'internCompanyRecipientList'])->name('evaluations.internCompanyRecipientList');
    Route::get('/evaluations/{evaluation}/manage-questions', [EvaluationController::class, 'manageQuestions'])->name('evaluations.manageQuestions');
    Route::post('/evaluations/{evaluation}/update-questions', [EvaluationController::class, 'updateQuestions'])->name('evaluations.updateQuestions');
    // Send evaluation to interns for intern_student evaluation type
    Route::post('/evaluations/{evaluation}/send-intern-student', [EvaluationController::class, 'sendInternStudentEvaluation'])->name('evaluations.sendInternStudent');

    // Recipient list for intern_student evaluations
    Route::get('/evaluations/intern_student/{evaluation}/recipients', [EvaluationController::class, 'internStudentRecipientList'])->name('evaluations.internStudentRecipientList');

    Route::get('evaluations/{evaluation}/student/{student}/view-scores', [EvaluationController::class, 'viewStudentScores'])->name('admin.evaluations.viewStudentScores');

    Route::get('evaluations/{evaluation}/student/{student}/download-scores-pdf', [EvaluationController::class, 'downloadStudentScoresPDF'])->name('admin.evaluations.downloadStudentScoresPDF');
    Route::get('/admin/evaluations/{evaluation}/student/{student}/scores', [EvaluationController::class, 'viewStudentScores'])->name('admin.evaluations.viewStudentScores');
});

// Evaluations Recipient Access
Route::middleware(['auth'])->group(function () {
    Route::post('/evaluations/{evaluation}/submit', [EvaluationController::class, 'storeResponse'])->name('evaluations.submitResponse');
    Route::get('/evaluations/{evaluation}/submit', [EvaluationController::class, 'showResponseForm'])->name('evaluations.showResponseForm');
    Route::get('/evaluations/available', [EvaluationController::class, 'recipientIndex'])->name('evaluations.recipientIndex');
    Route::get('/evaluations/{evaluation}/view-response', [EvaluationController::class, 'viewUserResponse'])->name('evaluations.viewUserResponse');
    Route::get('/evaluations/{evaluation}/downloadResponsePDF', [EvaluationController::class, 'downloadResponsePDF'])->name('evaluations.downloadResponsePDF');
    // Route for students to view evaluations given by companies
    Route::get('/evaluations/student/view/{evaluation}/{student}', [EvaluationController::class, 'viewStudentEvaluation'])->name('evaluations.viewStudentEvaluation');
    Route::get('/evaluations/student/download/{evaluation}/{student}', [EvaluationController::class, 'downloadStudentEvaluationPDF'])->name('evaluations.downloadStudentEvaluationPDF');

});

// Pullouts Request
// Admin Access (Super Admin and Admin)
Route::middleware(['auth', 'administrative'])->group(function () {
    Route::get('/api/company/{company}/students', [PulloutController::class, 'getStudentsByCompany']);
    Route::get('/pullouts', [PulloutController::class, 'index'])->name('pullouts.index');
    Route::get('/pullouts/create', [PulloutController::class, 'create'])->name('pullouts.create');
    Route::post('/pullouts', [PulloutController::class, 'store'])->name('pullouts.store');
});

// Company Access
Route::middleware(['auth'])->group(function () {
    Route::get('/pullouts/company', [PulloutController::class, 'companyIndex'])->name('pullouts.companyIndex');
    Route::get('/pullouts/{pullout}/respond', [PulloutController::class, 'showRespondForm'])->name('pullouts.showRespondForm');
    Route::post('/pullouts/{pullout}/respond', [PulloutController::class, 'respond'])->name('pullouts.respond');
});


// Student Internship Files Compilation

//Student Process
Route::middleware(['auth'])->group(function () {
    // Internship Files Routes
    Route::get('/internship-files', [InternshipFilesController::class, 'index'])->name('internship.files');
    Route::get('/internship-files/eod', [InternshipFilesController::class, 'viewEodReports'])->name('internship.files.eod');
    Route::get('/internship-files/dtr', [InternshipFilesController::class, 'viewDtrReports'])->name('internship.files.dtr');
    Route::post('/upload-monthly-report/{type}', [InternshipFilesController::class, 'uploadMonthlyReport'])->name('upload.monthly.report');
    Route::post('/upload-completion-file/{type}', [InternshipFilesController::class, 'uploadCompletionFile'])->name('upload.completion.file');
    Route::get('/download-all-files', [InternshipFilesController::class, 'downloadAllFiles'])->name('download.all.files');

    // Monthly Reports Preview Route
    Route::get('/internship-files/monthly-report/preview/{type}/{id}', [InternshipFilesController::class, 'previewMonthlyReport'])
        ->name('internship.files.monthly.preview');

    // Completion Requirements Preview Route
    Route::get('/internship-files/completion/preview/{type}/{id}', [InternshipFilesController::class, 'previewCompletionRequirement'])
        ->name('internship.files.completion.preview');

});

// Admin and Faculty View
Route::middleware(['auth', 'facultyaccess'])->group(function () {
    Route::get('/internship-files/{studentId}', [InternshipFilesController::class, 'viewStudentFiles'])->name('student.internship.files.view');
    Route::get('/internship-files/monthly/preview/{type}/{id}/{studentId?}', [InternshipFilesController::class, 'previewMonthlyReport'])
        ->name('admin.internship.files.monthly.preview');
});


// Excusal Requests
Route::middleware(['auth'])->group(function () {
    // Student routes
    Route::get('/requests/company', [RequestController::class, 'companyIndex'])->name('requests.companyIndex');
    Route::get('/requests/company/{request}', [RequestController::class, 'companyShow'])->name('requests.companyShow');
    Route::get('/requests', [RequestController::class, 'studentIndex'])->name('requests.studentIndex');
    Route::get('/requests/create', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{request}', [RequestController::class, 'studentShow'])->name('requests.studentShow');

    // Admin routes
    Route::middleware(['auth', 'administrative'])->group(function () {
        Route::get('/admin/requests', [RequestController::class, 'adminIndex'])->name('requests.adminIndex');
        Route::get('/admin/requests/{request}', [RequestController::class, 'show'])->name('requests.show');
        Route::post('/admin/requests/{request}/respond', [RequestController::class, 'respond'])->name('requests.respond');
    });

    Route::get('/requests/{request}/preview', [RequestController::class, 'preview'])->name('requests.preview');
});
