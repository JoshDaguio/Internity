@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Penalty Details</h1>
    <p>Violation: {{ $penalty->violation }}</p>
    <p>Penalty Hours: {{ $penalty->penalty_hours }}</p>
    <a href="{{ route('penalties.edit', $penalty) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('penalties.index') }}" class="btn btn-secondary">Back to List</a>
    <form action="{{ route('penalties.destroy', $penalty) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
</div>
@endsection
