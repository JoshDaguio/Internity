@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Edit Job</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Edit Job</li>
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
        <h5 class="card-title">Update Job Details</h5>

        <form action="{{ route('jobs.update', $job) }}" method="POST" class="row g-3">
            @csrf
            @method('PATCH')

            <!-- Job Title -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" id="title" name="title" class="form-control" value="{{ $job->title }}" placeholder="Job Title" required>
                    <label for="title">Job Title</label>
                </div>
            </div>

            <!-- Industry -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" id="industry" name="industry" class="form-control" value="{{ $job->industry }}" placeholder="Industry" required>
                    <label for="industry">Industry</label>
                </div>
            </div>

            <!-- Positions Available -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="number" id="positions_available" name="positions_available" class="form-control" value="{{ $job->positions_available }}" min="1" placeholder="Positions Available" required>
                    <label for="positions_available">Positions Available</label>
                </div>
            </div>

            <!-- Location -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" id="location" name="location" class="form-control" value="{{ $job->location }}" placeholder="Location" required>
                    <label for="location">Location</label>
                </div>
            </div>

            <!-- Work Type -->
            <div class="col-md-12">
                <div class="form-floating">
                    <select id="work_type" name="work_type" class="form-control" required>
                        <option value="Remote" {{ $job->work_type == 'Remote' ? 'selected' : '' }}>Remote</option>
                        <option value="On-site" {{ $job->work_type == 'On-site' ? 'selected' : '' }}>On-Site</option>
                        <option value="Hybrid" {{ $job->work_type == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                    <label for="work_type">Work Type</label>
                </div>
            </div>

            <!-- Schedule Section -->
            @php
                $schedule = json_decode($job->schedule, true);
            @endphp

            <div id="days_picker" class="col-md-12 mb-3">
                <label class="form-label">Select Work Days</label>
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                    <div class="form-check">
                        <input type="checkbox" name="schedule_days[]" value="{{ $day }}" class="form-check-input" id="{{ strtolower($day) }}"
                            @if(in_array($day, $schedule['days'])) checked @endif>
                        <label class="form-check-label" for="{{ strtolower($day) }}">{{ $day }}</label>
                    </div>
                @endforeach
            </div>

            <div id="hybrid_picker" class="col-md-12 mb-3" style="display:none;">
                <label class="form-label">Select On-site Days</label>
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                    <div class="form-check">
                        <input type="checkbox" name="onsite_days[]" value="{{ $day }}" class="form-check-input" id="onsite_{{ strtolower($day) }}"
                            @if(isset($schedule['onsite_days']) && in_array($day, $schedule['onsite_days'])) checked @endif>
                        <label class="form-check-label" for="onsite_{{ strtolower($day) }}">{{ $day }}</label>
                    </div>
                @endforeach

                <label class="form-label mt-3">Select Remote Days</label>
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                    <div class="form-check">
                        <input type="checkbox" name="remote_days[]" value="{{ $day }}" class="form-check-input" id="remote_{{ strtolower($day) }}"
                            @if(isset($schedule['remote_days']) && in_array($day, $schedule['remote_days'])) checked @endif>
                        <label class="form-check-label" for="remote_{{ strtolower($day) }}">{{ $day }}</label>
                    </div>
                @endforeach
            </div>

            <!-- Time Section -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="time" id="start_time" name="start_time" class="form-control" value="{{ $schedule['start_time'] }}" required>
                    <label for="start_time">Start Time</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-floating">
                    <input type="time" id="end_time" name="end_time" class="form-control" value="{{ $schedule['end_time'] }}" required>
                    <label for="end_time">End Time</label>
                </div>
            </div>

            <!-- Job Description -->
            <div class="col-md-12">
                <div class="form-floating">
                    <textarea id="description" name="description" class="form-control" placeholder="Description" style="height: 150px;" required>{{ $job->description }}</textarea>
                    <label for="description">Description</label>
                </div>
            </div>

            <!-- Job Qualification -->
            <div class="col-md-12">
                <div class="form-floating">
                    <textarea id="qualification" name="qualification" class="form-control" placeholder="Qualification" style="height: 150px;" required>{{ $job->qualification }}</textarea>
                    <label for="qualification">Qualification</label>
                </div>
            </div>

            <!-- Skill Tags -->
            <div class="col-md-12">
                    <select id="skill_tags" name="skill_tags[]" class="form-control" multiple="multiple">
                        @foreach($skillTags as $skillTag)
                            <option value="{{ $skillTag->id }}" {{ in_array($skillTag->id, $job->skillTags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $skillTag->name }}
                            </option>
                        @endforeach
                    </select>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('jobs.index') }}" class="btn btn-secondary">Cancel</a>
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
            placeholder: "Select preferred skills",
            allowClear: true
        });
    });
</script>

@endsection
