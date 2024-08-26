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
        </div>
    </div>

    <a href="{{ route('faculty.edit', $faculty->id) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('faculty.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
