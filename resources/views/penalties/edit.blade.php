@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Edit Penalty</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Violation and Penalties</li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Penalty Details</h5>
            <form action="{{ route('penalties.update', $penalty) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <label for="violation" class="form-label"><strong>Violation</strong></label>
                    <input type="text" id="violation" name="violation" class="form-control" value="{{ $penalty->violation }}" required>
                </div>
                @if ($penalty->penalty_type == 'fixed')
                <div class="mb-3">
                    <label for="penalty_hours" class="form-label"><strong>Penalty Hours</strong></label>
                    <input type="number" id="penalty_hours" name="penalty_hours" class="form-control" value="{{ $penalty->penalty_hours }}" required min="1">
                </div>
                @else
                <div class="mb-3">
                    <label for="penalty_hours" class="form-label"><strong>Penalty Hours</strong></label>
                    <input type="number" id="penalty_hours" name="penalty_hours" class="form-control" value="{{ $penalty->penalty_hours }}" required min="1" disabled>
                    <div class="form-text">
                        <p><strong><code>Conditional Penalties Are Automatically Implemented Depending on Student's DTR</code></strong></p>
                    </div>
                </div>
                @endif
                
                @if ($penalty->penalty_type == 'conditional')
                <div class="mb-3">
                    <label for="conditions" class="form-label"><strong>Conditions</strong></label>
                    <input type="text" id="conditions" name="conditions" class="form-control" value="{{ $penalty->conditions }}">
                </div>
                @else
                <div class="mb-3">
                    <label for="conditions" class="form-label"><strong>Conditions</strong></label>
                    <input type="text" id="conditions" name="conditions" class="form-control" value="{{ $penalty->conditions }}" disabled>
                    <div class="form-text">
                        <p><strong><code>Editing Conditions Are Only Availabe For Conditional Penalty Type</code></strong></p>
                    </div>
                </div>
                @endif

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    <a href="{{ route('penalties.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
