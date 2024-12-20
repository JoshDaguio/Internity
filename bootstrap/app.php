<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\Super_admin::class,
            'admin' => \App\Http\Middleware\Admin::class,
            'faculty' => \App\Http\Middleware\Faculty::class,
            'company' => \App\Http\Middleware\Company::class,
            'student' => \App\Http\Middleware\Student::class,
            'administrative' => \App\Http\Middleware\Administrative::class,
            'job_access' => \App\Http\Middleware\JobAccess::class,
            'facultyaccess' => \App\Http\Middleware\FacultyAccess::class,
            'studentmonitoring' => \App\Http\Middleware\StudentMonitoring::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
