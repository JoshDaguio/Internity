@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Create Student Account</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Student</li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Student Account Form</h5>

            <form method="POST" action="{{ route('students.store') }}" class="row g-3 needs-validation" novalidate>
                @csrf

                <div class="col-md-12">
                    <div class="form-floating">
                        <input type="text" name="first_name" class="form-control" id="first_name" value="{{ old('first_name') }}" placeholder="First Name" required>
                        <label for="first_name">First Name</label>
                        <div class="invalid-feedback">Please enter the first name.</div>
                    </div>
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>

                <div class="col-md-12">
                    <div class="form-floating">
                        <input type="text" name="last_name" class="form-control" id="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required>
                        <label for="last_name">Last Name</label>
                        <div class="invalid-feedback">Please enter the last name.</div>
                    </div>
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>

                <div class="col-md-12">
                    <div class="form-floating">
                        <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="Email" required>
                        <label for="email">AUF Email (lastname.firstname@auf.edu.ph)</label>
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="col-md-12">
                    <div class="form-floating">
                        <input type="text" name="id_number" class="form-control" id="id_number" value="{{ old('id_number') }}" placeholder="ID Number" required>
                        <label for="id_number">ID Number (00-0000-000)</label>
                        <div class="invalid-feedback">Please enter a valid ID number.</div>
                    </div>
                    <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                </div>

                <div class="col-md-12">
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2) <!-- Super Admin or Admin -->
                        <div class="form-floating">
                            <select name="course_id" class="form-select" id="course_id" required>
                                <option value="">Select a course</option> <!-- Optional placeholder -->
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_code }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="course_id">Course</label>
                            <div class="invalid-feedback">Please select a course.</div>
                        </div>
                        <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                    @else <!-- For faculty, set the course automatically -->
                    
                        <input type="hidden" name="course_id" value="{{ Auth::user()->course_id }}">
                        <div class="form-floating">
                            <input type="text" class="form-control" value="{{ Auth::user()->course->course_code }}" disabled>
                            <label>Course</label>
                        </div>
                        <div class="form-text">
                            <p><strong><code>Faculty Users Can Only Add Students From Their Course</code></strong></p>
                        </div>
                    @endif
                </div>


                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('students.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection

<script>
    // JavaScript for Bootstrap validation
    (function () {
        'use strict'

        const forms = document.querySelectorAll('.needs-validation')

        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
