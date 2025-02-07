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
        <p>
            <h5>
                <strong>
                    <span class="badge bg-info text-dark"><i class="bi bi-calendar"></i> Current Date:</span>
                </strong> 
            </h5>
            <iframe src="https://free.timeanddate.com/clock/i9lf5ga0/n2280/fn14/fs24/ftb/tt0/tw0/tm1/th2/tb2" frameborder="0" width="326" height="31"></iframe>
        </p>

        <form action="{{ route('end_of_day_reports.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="form-floating mb-3">
                <input type="text" 
                    class="form-control" 
                    id="submission_for_date" 
                    value="{{ request('submission_date') ? \Carbon\Carbon::parse(request('submission_date'))->format('F d, Y') : \Carbon\Carbon::now()->format('F d, Y') }}" 
                    disabled>
                <label for="submission_for_date">Submission for Date</label>
            </div>
            <input type="hidden" name="submission_date" value="{{ request('submission_date') }}">

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
                                <input type="number" name="tasks[0][time_hours]" id="time_hours_0" class="form-control" placeholder="Hours" min="0">
                                <label for="time_hours_0">Hours</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" name="tasks[0][time_minutes]" id="time_minutes_0" class="form-control" placeholder="Minutes" min="0" max="59">
                                <label for="time_minutes_0">Minutes</label>
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
                <button type="submit" class="btn btn-primary btn-sm">Submit Report</button>
                <a href="{{ route('end_of_day_reports.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
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
        newTask.setAttribute('id', `task_${taskCount}`);
        newTask.innerHTML = `
            <div class="form-floating mb-3">
                <textarea name="tasks[${taskCount}][task_description]" id="task_description_${taskCount}" class="form-control" placeholder="Task Description" required></textarea>
                <label for="task_description_${taskCount}">Task Description</label>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="number" name="tasks[${taskCount}][time_hours]" id="time_hours_${taskCount}" class="form-control" placeholder="Hours" min="0">
                        <label for="time_hours_${taskCount}">Hours</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="number" name="tasks[${taskCount}][time_minutes]" id="time_minutes_${taskCount}" class="form-control" placeholder="Minutes" min="0" max="59">
                        <label for="time_minutes_${taskCount}">Minutes</label>
                    </div>
                </div>
            </div>
            <div class="text-end mb-2">
                <button type="button" class="btn btn-danger remove-task btn-sm" onclick="removeTask(${taskCount})">Remove Task</button>
            </div>
        `;
        tasksContainer.appendChild(newTask);
        taskCount++;
        updateRemoveButtons();
    });

    function removeTask(taskId) {
        const taskElement = document.getElementById(`task_${taskId}`);
        if (taskElement) {
            taskElement.remove();
            updateRemoveButtons();
        }
    }

    function updateRemoveButtons() {
        const taskElements = document.querySelectorAll('.task');
        const removeButtons = document.querySelectorAll('.remove-task');
        
        if (taskElements.length === 1) {
            removeButtons.forEach(button => button.style.display = 'none');
        } else {
            removeButtons.forEach(button => button.style.display = 'inline-block');
        }
    }

    document.addEventListener('DOMContentLoaded', updateRemoveButtons);

</script>

@endsection
