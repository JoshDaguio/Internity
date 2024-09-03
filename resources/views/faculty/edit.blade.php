@extends('layouts.app')

@section('body')
    <h1>Edit Faculty Account</h1>
    <form action="{{ route('faculty.update', $faculty) }}" method="POST">
        @csrf
        @method('PATCH')
        
        <!-- First Name -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $faculty->profile->first_name ?? '' }}" required>
        </div>
        
        <!-- Last Name -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $faculty->profile->last_name ?? '' }}" required>
        </div>
        
        <!-- ID Number -->
        <div class="mb-3">
            <label for="id_number" class="form-label">ID Number</label>
            <input type="text" class="form-control" id="id_number" name="id_number" value="{{ $faculty->profile->id_number ?? '' }}" required>
        </div>
        
        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $faculty->email }}" required>
        </div>
        
        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password (leave blank to keep current password)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <!-- Course Selection -->
        <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select name="course_id" id="course_id" class="form-control">
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $course->id == $faculty->course_id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Submit and Cancel Buttons -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-2">Update</button>
            <a href="{{ route('faculty.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection