@extends('layouts.app')

@section('body')
    <h1>Edit Job</h1>
    <form action="{{ route('jobs.update', $job) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="title" class="form-label">Job Title</label>
            <input type="text" id="title" name="title" class="form-control" value="{{ $job->title }}" required>
        </div>

        <div class="mb-3">
            <label for="industry" class="form-label">Industry</label>
            <input type="text" id="industry" name="industry" class="form-control" value="{{ $job->industry }}" required>
        </div>

        <div class="mb-3">
            <label for="positions_available" class="form-label">Positions Available</label>
            <input type="number" id="positions_available" name="positions_available" class="form-control" value="{{ $job->positions_available }}" required min="1">
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" class="form-control" value="{{ $job->location }}" required>
        </div>

        <div class="mb-3">
            <label for="work_type" class="form-label">Work Type</label>
            <select id="work_type" name="work_type" class="form-control" required>
                <option value="Remote" {{ $job->work_type == 'Remote' ? 'selected' : '' }}>Remote</option>
                <option value="On-site" {{ $job->work_type == 'On-site' ? 'selected' : '' }}>On-Site</option>
                <option value="Hybrid" {{ $job->work_type == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
            </select>
        </div>

        <!-- Schedule Section -->
        @php
            $schedule = json_decode($job->schedule, true);
        @endphp

        <div id="days_picker" class="mb-3">
            <label class="form-label">Select Work Days</label>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Monday" class="form-check-input" id="monday" 
                       @if(in_array('Monday', $schedule['days'])) checked @endif>
                <label class="form-check-label" for="monday">Monday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Tuesday" class="form-check-input" id="tuesday"
                       @if(in_array('Tuesday', $schedule['days'])) checked @endif>
                <label class="form-check-label" for="tuesday">Tuesday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Wednesday" class="form-check-input" id="wednesday"
                       @if(in_array('Wednesday', $schedule['days'])) checked @endif>
                <label class="form-check-label" for="wednesday">Wednesday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Thursday" class="form-check-input" id="thursday"
                       @if(in_array('Thursday', $schedule['days'])) checked @endif>
                <label class="form-check-label" for="thursday">Thursday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Friday" class="form-check-input" id="friday"
                       @if(in_array('Friday', $schedule['days'])) checked @endif>
                <label class="form-check-label" for="friday">Friday</label>
            </div>
        </div>

        <div id="hybrid_picker" class="mb-3" style="display:none;">
            <label class="form-label">Select On-site Days</label>
            <!-- On-site days -->
            <div class="form-check">
                <input type="checkbox" name="onsite_days[]" value="Monday" class="form-check-input" id="onsite_monday"
                       @if(isset($schedule['onsite_days']) && in_array('Monday', $schedule['onsite_days'])) checked @endif>
                <label class="form-check-label" for="onsite_monday">Monday</label>
            </div>
            <!-- Repeat for other days -->
            <label class="form-label">Select Remote Days</label>
            <!-- Remote days -->
            <div class="form-check">
                <input type="checkbox" name="remote_days[]" value="Monday" class="form-check-input" id="remote_monday"
                       @if(isset($schedule['remote_days']) && in_array('Monday', $schedule['remote_days'])) checked @endif>
                <label class="form-check-label" for="remote_monday">Monday</label>
            </div>
            <!-- Repeat for other days -->
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="time" id="start_time" name="start_time" class="form-control" value="{{ $schedule['start_time'] }}" required>

            <label for="end_time" class="form-label mt-3">End Time</label>
            <input type="time" id="end_time" name="end_time" class="form-control" value="{{ $schedule['end_time'] }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4" required>{{ $job->description }}</textarea>
        </div>

        <div class="mb-3">
            <label for="qualification" class="form-label">Qualification</label>
            <textarea id="qualification" name="qualification" class="form-control" rows="4" required>{{ $job->qualification }}</textarea>
        </div>

        <div class="mb-3">
            <label for="preferred_skills" class="form-label">Preferred Skills</label>
            <textarea id="preferred_skills" name="preferred_skills" class="form-control" rows="4" required>{{ $job->preferred_skills }}</textarea>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-2">Update</button>
            <a href="{{ route('jobs.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

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
    </script>
@endsection
