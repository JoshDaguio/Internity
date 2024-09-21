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

            <form method="POST" action="{{ route('students.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" name="first_name" class="form-control" id="first_name" value="{{ old('first_name') }}" placeholder="First Name" required>
                            <label for="first_name">First Name</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" name="last_name" class="form-control" id="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required>
                            <label for="last_name">Last Name</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" placeholder="Email" required>
                            <label for="email">Email</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" name="id_number" class="form-control" id="id_number" value="{{ old('id_number') }}" placeholder="ID Number" required>
                            <label for="id_number">ID Number</label>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-floating">
                            <select name="course_id" class="form-select" id="course_id" required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_code }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="course_id">Course</label>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Create</button>
                    <a href="{{ route('students.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection
