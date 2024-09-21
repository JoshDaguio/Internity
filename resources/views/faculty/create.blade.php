@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Add New Faculty</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Faculty</li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Create Faculty Account Form</h5>

        <form action="{{ route('faculty.store') }}" method="POST" class="row g-3">
            @csrf

            <!-- First Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingFirstName" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required>
                    <label for="floatingFirstName">First Name</label>
                </div>
            </div>

            <!-- Last Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingLastName" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required>
                    <label for="floatingLastName">Last Name</label>
                </div>
            </div>

            <!-- ID Number -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingIDNumber" name="id_number" value="{{ old('id_number') }}" placeholder="ID Number" required>
                    <label for="floatingIDNumber">ID Number</label>
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="email" class="form-control" id="floatingEmail" name="email" value="{{ old('email') }}" placeholder="Email" required>
                    <label for="floatingEmail">Email</label>
                </div>
            </div>

            <!-- Course Selection -->
            <div class="col-md-12">
                <div class="form-floating">
                    <select name="course_id" id="floatingCourse" class="form-select" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                    <label for="floatingCourse">Course</label>
                </div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('faculty.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
