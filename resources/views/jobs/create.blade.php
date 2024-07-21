@extends('layouts.app')

@section('body')
    <h1>Create Job</h1>
    <form action="{{ route('jobs.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Job Title</label>
            <input type="text" id="title" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="industry" class="form-label">Industry</label>
            <input type="text" id="industry" name="industry" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="positions_available" class="form-label">Positions Available</label>
            <input type="number" id="positions_available" name="positions_available" class="form-control" required min="1">
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="work_type" class="form-label">Work Type</label>
            <select id="work_type" name="work_type" class="form-control" required>
                <option value="remote">Remote</option>
                <option value="on-site">On-site</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="schedule" class="form-label">Schedule</label>
            <input type="text" id="schedule" name="schedule" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="qualification" class="form-label">Qualification</label>
            <textarea id="qualification" name="qualification" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label for="preferred_skills" class="form-label">Preferred Skills</label>
            <textarea id="preferred_skills" name="preferred_skills" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection
