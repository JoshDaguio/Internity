@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Student Details</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title">{{ $student->profile ? $student->profile->first_name . ' ' . $student->profile->last_name : 'N/A' }}</h3>
            <p class="card-text"><strong>Email:</strong> {{ $student->email }}</p>
            <p class="card-text"><strong>ID Number:</strong> {{ $student->profile ? $student->profile->id_number : 'N/A' }}</p>
            <p class="card-text"><strong>Course:</strong> {{ $student->course ? $student->course->course_name : 'N/A' }}</p>
            <p class="card-text"><strong>Status:</strong> {{ $student->status_id == 1 ? 'Active' : 'Inactive' }}</p>
        </div>
    </div>

    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('students.list') }}" class="btn btn-secondary">Back to List</a>
    @if($student->status_id == 1)
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $student->id }}">
            Deactivate
        </button>
    @else
        <form action="{{ route('students.reactivate', $student->id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">Reactivate</button>
        </form>
    @endif

    <!-- Deactivate Modal -->
    <div class="modal fade" id="deactivateModal-{{ $student->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel-{{ $student->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deactivateModalLabel-{{ $student->id }}">Deactivate Student Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to deactivate this student account?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('students.deactivate', $student->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Deactivate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection