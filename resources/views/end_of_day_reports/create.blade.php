@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Create End of Day Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Reports</li>
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
        <h5 class="card-title">New End of Day Report Form</h5>

        <form action="{{ route('end_of_day_reports.store') }}" method="POST" class="row g-3">
            @csrf

            <div id="tasks-container" class="col-md-12">
                <h3>Daily Tasks</h3>
                <div class="task">
                    <!-- Task Description -->
                    <div class="form-floating mb-3">
                        <textarea name="tasks[0][task_description]" id="task_description_0" class="form-control" placeholder="Task Description" required></textarea>
                        <label for="task_description_0">Task Description</label>
                    </div>

                    <!-- Time Spent -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="tasks[0][time_spent]" id="time_spent_0" class="form-control" placeholder="Time Spent" required min="1">
                                <label for="time_spent_0">Time Spent</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select name="tasks[0][time_unit]" id="time_unit_0" class="form-select" required>
                                    <option value="minutes">Minutes</option>
                                    <option value="hours">Hours</option>
                                </select>
                                <label for="time_unit_0">Time Unit</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" id="add-task" class="btn btn-secondary mb-3">Add Another Task</button>

            <!-- Key Successes -->
            <div class="form-floating mb-3">
                <textarea name="key_successes" id="key_successes" class="form-control" placeholder="Key Successes" required></textarea>
                <label for="key_successes">Key Successes</label>
            </div>

            <!-- Main Challenges -->
            <div class="form-floating mb-3">
                <textarea name="main_challenges" id="main_challenges" class="form-control" placeholder="Main Challenges" required></textarea>
                <label for="main_challenges">Main Challenges</label>
            </div>

            <!-- Plans for Tomorrow -->
            <div class="form-floating mb-3">
                <textarea name="plans_for_tomorrow" id="plans_for_tomorrow" class="form-control" placeholder="Plans for Tomorrow" required></textarea>
                <label for="plans_for_tomorrow">Plans for Tomorrow</label>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit Report</button>
                <a href="{{ route('end_of_day_reports.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    let taskCount = 1;

    document.getElementById('add-task').addEventListener('click', function() {
        const tasksContainer = document.getElementById('tasks-container');
        const newTask = document.createElement('div');
        newTask.classList.add('task');
        newTask.innerHTML = `
            <div class="form-floating mb-3">
                <textarea name="tasks[${taskCount}][task_description]" id="task_description_${taskCount}" class="form-control" placeholder="Task Description" required></textarea>
                <label for="task_description_${taskCount}">Task Description</label>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="number" name="tasks[${taskCount}][time_spent]" id="time_spent_${taskCount}" class="form-control" placeholder="Time Spent" required min="1">
                        <label for="time_spent_${taskCount}">Time Spent</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <select name="tasks[${taskCount}][time_unit]" id="time_unit_${taskCount}" class="form-select" required>
                            <option value="minutes">Minutes</option>
                            <option value="hours">Hours</option>
                        </select>
                        <label for="time_unit_${taskCount}">Time Unit</label>
                    </div>
                </div>
            </div>
        `;
        tasksContainer.appendChild(newTask);
        taskCount++;
    });
</script>

@endsection
