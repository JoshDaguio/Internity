@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Create Job</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Create Job</li>
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
        <h5 class="card-title">New Job Form</h5>

        <form action="{{ route('jobs.store') }}" method="POST" class="row g-3">
            @csrf

            <!-- Job Title -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" id="title" name="title" class="form-control" placeholder="Job Title" required>
                    <label for="title">Job Title</label>
                </div>
            </div>

            <!-- Industry -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" id="industry" name="industry" class="form-control" placeholder="Industry" required>
                    <label for="industry">Industry</label>
                </div>
            </div>

            <!-- Positions Available -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="number" id="positions_available" name="positions_available" class="form-control" min="1" placeholder="Positions Available" required>
                    <label for="positions_available">Positions Available</label>
                </div>
            </div>

            <!-- Location -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" id="location" name="location" class="form-control" placeholder="Location" required>
                    <label for="location">Location</label>
                </div>
            </div>

            <!-- Work Type -->
            <div class="col-md-12">
                <div class="form-floating">
                    <select id="work_type" name="work_type" class="form-control" required>
                        <option value="Remote">Remote</option>
                        <option value="On-site">On-Site</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                    <label for="work_type">Work Type</label>
                </div>
            </div>

            <!-- Schedule Section -->
            <div id="days_picker" class="col-md-12 mb-3">
                <label class="form-label">Select Work Days</label>
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                    <div class="form-check">
                        <input type="checkbox" name="schedule_days[]" value="{{ $day }}" class="form-check-input" id="{{ strtolower($day) }}">
                        <label class="form-check-label" for="{{ strtolower($day) }}">{{ $day }}</label>
                    </div>
                @endforeach
            </div>

            <div id="hybrid_picker" class="col-md-12 mb-3" style="display:none;">
                <label class="form-label">Select On-site Days</label>
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                    <div class="form-check">
                        <input type="checkbox" name="onsite_days[]" value="{{ $day }}" class="form-check-input" id="onsite_{{ strtolower($day) }}">
                        <label class="form-check-label" for="onsite_{{ strtolower($day) }}">{{ $day }}</label>
                    </div>
                @endforeach

                <label class="form-label mt-3">Select Remote Days</label>
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                    <div class="form-check">
                        <input type="checkbox" name="remote_days[]" value="{{ $day }}" class="form-check-input" id="remote_{{ strtolower($day) }}">
                        <label class="form-check-label" for="remote_{{ strtolower($day) }}">{{ $day }}</label>
                    </div>
                @endforeach
            </div>

            <!-- Time Section -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="time" id="start_time" name="start_time" class="form-control" required>
                    <label for="start_time">Start Time</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="time" id="end_time" name="end_time" class="form-control" required>
                    <label for="end_time">End Time</label>
                </div>
            </div>

            <!-- Job Description -->
            <div class="col-md-12">
                <div class="form-floating">
                    <textarea id="description" name="description" class="form-control" placeholder="Description" style="height: 150px;" required></textarea>
                    <label for="description">Description</label>
                </div>
            </div>

            <!-- Job Qualification -->
            <div class="col-md-12">
                <div class="form-floating">
                    <textarea id="qualification" name="qualification" class="form-control" placeholder="Qualification" style="height: 150px;" required></textarea>
                    <label for="qualification">Qualification</label>
                </div>
            </div>

            <!-- Skill Tags -->
            <div class="col-md-12">
                    <select id="skill_tags" name="skill_tags[]" class="form-control" multiple="multiple">
                        @foreach($skillTags as $skillTag)
                            <option value="{{ $skillTag->id }}">{{ $skillTag->name }}</option>
                        @endforeach
                    </select>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-sm">Create</button>
                <a href="{{ route('jobs.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const workType = document.getElementById('work_type');
        const hybridPicker = document.getElementById('hybrid_picker');
        const daysPicker = document.getElementById('days_picker');

        workType.addEventListener('change', function () {
            if (this.value === 'Hybrid') {
                hybridPicker.style.display = 'block';
                daysPicker.style.display = 'none';
            } else {
                hybridPicker.style.display = 'none';
                daysPicker.style.display = 'block';
            }
        });

        // Trigger the change event on load to handle default value
        workType.dispatchEvent(new Event('change'));
    });

    $(document).ready(function() {
        $('#skill_tags').select2({
            placeholder: "Select preferred skills (Optional)",
            allowClear: true
        });
    });
</script>

@endsection
