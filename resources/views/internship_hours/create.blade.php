@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Create Internship Hours</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Internship Hours</li>
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
        <h5 class="card-title">Create Internship Hours Form</h5>

        <form action="{{ route('internship_hours.store') }}" method="POST" class="row g-3">
            @csrf

            <!-- Course Selection -->
            <div class="col-md-12">
                <div class="form-floating">
                    <select id="course_id" name="course_id" class="form-select" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }}</option>
                        @endforeach
                    </select>
                    <label for="course_id">Course</label>
                </div>
            </div>

            <!-- Hours -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="number" class="form-control" id="floatingHours" name="hours" placeholder="Internship Hours" min="1" required>
                    <label for="floatingHours">Hours</label>
                </div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('internship_hours.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
