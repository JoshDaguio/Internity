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

//For Password Reset
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
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

route::get('super_admin/dashboard',[SuperAdminController::class,'index'])->
    middleware(['auth','super_admin']);
route::get('admin/dashboard',[AdminController::class,'index'])->
    middleware(['auth','admin']);
route::get('student/dashboard',[StudentController::class,'index'])->
    middleware(['auth','student']);

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

    Route::get('/students/{student}/show', [AdminController::class, 'showStudent'])->name('students.show');
    Route::get('/students/{student}/edit', [AdminController::class, 'editStudent'])->name('students.edit');
    Route::patch('/students/{student}', [AdminController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{student}/deactivate', [AdminController::class, 'deactivateStudent'])->name('students.deactivate');
    Route::patch('/students/{student}/reactivate', [AdminController::class, 'reactivateStudent'])->name('students.reactivate');
});


//Registration Success - Currently not working
Route::get('/register/success', function () {
    return view('auth.register-success');
})->name('register.success');

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

