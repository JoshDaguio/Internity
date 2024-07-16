<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\StudentController;

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