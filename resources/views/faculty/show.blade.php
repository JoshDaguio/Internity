@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Faculty Details</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title">{{ $faculty->profile ? $faculty->profile->first_name . ' ' . $faculty->profile->last_name : 'N/A' }}</h3>
            <p class="card-text"><strong>Email:</strong> {{ $faculty->email }}</p>
            <p class="card-text"><strong>ID Number:</strong> {{ $faculty->profile ? $faculty->profile->id_number : 'N/A' }}</p>
            <p class="card-text"><strong>Course:</strong> {{ $faculty->course ? $faculty->course->course_name : 'N/A' }}</p>
            <p class="card-text"><strong>Status:</strong> {{ $faculty->status_id == 1 ? 'Active' : 'Inactive' }}</p>

            @if($faculty->status_id == 1)
                <!-- Deactivate Button -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $faculty->id }}">
                    Deactivate
                </button>
            @else
                <!-- Reactivate Button -->
                <form action="{{ route('faculty.reactivate', $faculty->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Reactivate</button>
                </form>
            @endif
        </div>
    </div>

    <a href="{{ route('faculty.edit', $faculty->id) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('faculty.index') }}" class="btn btn-secondary">Back to List</a>
</div>

<!-- Deactivate Modal -->
@if($faculty->status_id == 1)
    <div class="modal fade" id="deactivateModal-{{ $faculty->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel-{{ $faculty->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deactivateModalLabel-{{ $faculty->id }}">Deactivate Faculty Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to deactivate this faculty account?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('faculty.destroy', $faculty->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Deactivate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
