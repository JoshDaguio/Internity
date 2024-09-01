@extends('layouts.app')

@section('body')
    <h1>Create End of Day Report</h1>
    <form action="{{ route('end_of_day_reports.store') }}" method="POST">
        @csrf
        <div id="tasks-container">
            <h3>Daily Tasks</h3>
            <div class="task">
                <div class="mb-3">
                    <label for="task_description" class="form-label">Task Description</label>
                    <textarea name="tasks[0][task_description]" id="task_description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="time_spent" class="form-label">Time Spent</label>
                    <input type="number" name="tasks[0][time_spent]" class="form-control" required min="1">
                    <select name="tasks[0][time_unit]" class="form-select">
                        <option value="minutes">Minutes</option>
                        <option value="hours">Hours</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="button" id="add-task" class="btn btn-secondary">Add Another Task</button>
        <div class="mb-3">
            <label for="key_successes" class="form-label">Key Successes</label>
            <textarea name="key_successes" id="key_successes" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="main_challenges" class="form-label">Main Challenges</label>
            <textarea name="main_challenges" id="main_challenges" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="plans_for_tomorrow" class="form-label">Plans for Tomorrow</label>
            <textarea name="plans_for_tomorrow" id="plans_for_tomorrow" class="form-control" required></textarea>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Submit Report</button>
            <a href="{{ route('end_of_day_reports.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <script>
        let taskCount = 1;
        document.getElementById('add-task').addEventListener('click', function() {
            const tasksContainer = document.getElementById('tasks-container');
            const newTask = document.createElement('div');
            newTask.classList.add('task');
            newTask.innerHTML = `
                <div class="mb-3">
                    <label for="task_description_${taskCount}" class="form-label">Task Description</label>
                    <textarea name="tasks[${taskCount}][task_description]" id="task_description_${taskCount}" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="time_spent_${taskCount}" class="form-label">Time Spent</label>
                    <input type="number" name="tasks[${taskCount}][time_spent]" class="form-control" required min="1">
                    <select name="tasks[${taskCount}][time_unit]" class="form-select">
                        <option value="minutes">Minutes</option>
                        <option value="hours">Hours</option>
                    </select>
                </div>
            `;
            tasksContainer.appendChild(newTask);
            taskCount++;
        });
    </script>
@endsection
