@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Create Penalty</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Violation and Penalties</li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Penalties List</h5>
            <form action="{{ route('penalties.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="violation" class="form-label">Violation</label>
                    <input type="text" id="violation" name="violation" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="penalty_type" class="form-label">Penalty Type</label>
                    <select name="penalty_type" id="penalty_type" class="form-control" required>
                        <option value="fixed">Fixed Penalty</option>
                        <option value="conditional">Conditional Penalty</option>
                    </select>
                </div>

                <div class="mb-3" id="penalty_hours_section">
                    <label for="penalty_hours" class="form-label">Penalty Hours</label>
                    <input type="number" id="penalty_hours" name="penalty_hours" class="form-control" min="1" required>
                </div>

                <div class="mb-3" id="conditions_section" style="display:none;">
                    <label for="conditions" class="form-label">Conditions (e.g., "1 hour for every 10 minutes late")</label>
                    <input type="text" id="conditions" name="conditions" class="form-control">
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Create</button>
                    <a href="{{ route('penalties.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<script>
    document.getElementById('penalty_type').addEventListener('change', function() {
        if (this.value === 'conditional') {
            document.getElementById('penalty_hours_section').style.display = 'none';
            document.getElementById('conditions_section').style.display = 'block';
        } else {
            document.getElementById('penalty_hours_section').style.display = 'block';
            document.getElementById('conditions_section').style.display = 'none';
        }
    });
</script>
@endsection
