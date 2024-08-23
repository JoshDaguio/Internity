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


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile.profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

route::get('super_admin/dashboard',[SuperAdminController::class,'index'])->
    middleware(['auth','super_admin']);
route::get('admin/dashboard',[AdminController::class,'index'])->
    middleware(['auth','admin']);
route::get('faculty/dashboard',[FacultyController::class,'index'])->
    middleware(['auth','faculty']);
route::get('company/dashboard',[CompanyController::class,'index'])->
    middleware(['auth','company']);
route::get('student/dashboard',[StudentController::class,'index'])->
    middleware(['auth','student']);

//paste for each roles
route::get('/super_admin/dashboard', [SuperAdminController::class, 'index'])->middleware(['auth','super_admin'])->name('super_admin.dashboard');
route::get('/admin/dashboard', [AdminController::class, 'index'])->middleware(['auth','admin'])->name('admin.dashboard');
route::get('/faculty/dashboard', [FacultyController::class, 'index'])->middleware(['auth','faculty'])->name('faculty.dashboard');
route::get('/company/dashboard', [CompanyController::class, 'index'])->middleware(['auth','company'])->name('company.dashboard');
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
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
    Route::patch('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
});

//Student Registration Process
Route::middleware(['auth', 'facultyaccess'])->group(function () {
    Route::get('/registrations/pending', [AdminController::class, 'pendingRegistrations'])->name('registrations.pending');
    Route::post('/registrations/approve/{user}', [AdminController::class, 'approveRegistration'])->name('registrations.approve');
    Route::get('/students/list', [AdminController::class, 'approvedStudents'])->name('students.list');
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
});


//EOD Reports CRUD for all roles with their respective access rights
Route::middleware(['auth'])->group(function () {
    // Students can create and view their own reports
    Route::get('/end_of_day_reports', [EndOfDayReportController::class, 'index'])->name('end_of_day_reports.index');
    Route::get('/end_of_day_reports/create', [EndOfDayReportController::class, 'create'])->name('end_of_day_reports.create');
    Route::post('/end_of_day_reports', [EndOfDayReportController::class, 'store'])->name('end_of_day_reports.store');
    
    // Show specific report for the user (student, super admin, admin, faculty)
    Route::get('/end_of_day_reports/{report}', [EndOfDayReportController::class, 'show'])->name('end_of_day_reports.show');
});
