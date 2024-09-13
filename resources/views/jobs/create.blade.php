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
                <option value="Remote">Remote</option>
                <option value="On-site">On-Site</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </div>

        <!-- Schedule Section -->
        <div id="days_picker" class="mb-3">
            <label class="form-label">Select Work Days</label>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Monday" class="form-check-input" id="monday">
                <label class="form-check-label" for="monday">Monday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Tuesday" class="form-check-input" id="tuesday">
                <label class="form-check-label" for="tuesday">Tuesday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Wednesday" class="form-check-input" id="wednesday">
                <label class="form-check-label" for="wednesday">Wednesday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Thursday" class="form-check-input" id="thursday">
                <label class="form-check-label" for="thursday">Thursday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="schedule_days[]" value="Friday" class="form-check-input" id="friday">
                <label class="form-check-label" for="friday">Friday</label>
            </div>
        </div>

        <div id="hybrid_picker" class="mb-3" style="display:none;">
            <label class="form-label">Select On-site Days</label>
            <!-- On-site days -->
            <div class="form-check">
                <input type="checkbox" name="onsite_days[]" value="Monday" class="form-check-input" id="onsite_monday">
                <label class="form-check-label" for="onsite_monday">Monday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="onsite_days[]" value="Tuesday" class="form-check-input" id="onsite_tuesday">
                <label class="form-check-label" for="onsite_tuesday">Tuesday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="onsite_days[]" value="Wednesday" class="form-check-input" id="onsite_wednesday">
                <label class="form-check-label" for="onsite_wednesday">Wednesday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="onsite_days[]" value="Thursday" class="form-check-input" id="onsite_thursday">
                <label class="form-check-label" for="onsite_thursday">Thursday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="onsite_days[]" value="Friday" class="form-check-input" id="onsite_friday">
                <label class="form-check-label" for="onsite_friday">Friday</label>
            </div>

            <label class="form-label mt-3">Select Remote Days</label>
            <!-- Remote days -->
            <div class="form-check">
                <input type="checkbox" name="remote_days[]" value="Monday" class="form-check-input" id="remote_monday">
                <label class="form-check-label" for="remote_monday">Monday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="remote_days[]" value="Tuesday" class="form-check-input" id="remote_tuesday">
                <label class="form-check-label" for="remote_tuesday">Tuesday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="remote_days[]" value="Wednesday" class="form-check-input" id="remote_wednesday">
                <label class="form-check-label" for="remote_wednesday">Wednesday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="remote_days[]" value="Thursday" class="form-check-input" id="remote_thursday">
                <label class="form-check-label" for="remote_thursday">Thursday</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="remote_days[]" value="Friday" class="form-check-input" id="remote_friday">
                <label class="form-check-label" for="remote_friday">Friday</label>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="time" id="start_time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="end_time" class="form-label">End Time</label>
                <input type="time" id="end_time" name="end_time" class="form-control" required>
            </div>
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
            <label for="skill_tags" class="form-label">Preferred Skills (Optional)</label>
            <select id="skill_tags" name="skill_tags[]" class="form-control" multiple="multiple">
                @foreach($skillTags as $skillTag)
                    <option value="{{ $skillTag->id }}">{{ $skillTag->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-2">Create</button>
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

        $(document).ready(function() {
            $('#skill_tags').select2({
                placeholder: "Select preferred skills",
                allowClear: true
            });
        });
    </script>
@endsection
