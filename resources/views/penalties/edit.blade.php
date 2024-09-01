@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Edit Penalty</h1>
    <form action="{{ route('penalties.update', $penalty) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label for="violation" class="form-label">Violation</label>
            <input type="text" id="violation" name="violation" class="form-control" value="{{ $penalty->violation }}" required>
        </div>
        <div class="mb-3">
            <label for="penalty_hours" class="form-label">Penalty Hours</label>
            <input type="number" id="penalty_hours" name="penalty_hours" class="form-control" value="{{ $penalty->penalty_hours }}" required min="1">
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('penalties.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
