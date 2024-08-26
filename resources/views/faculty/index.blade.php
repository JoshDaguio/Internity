@extends('layouts.app')

@section('body')
    <h1>Faculty Accounts</h1>
    <a href="{{ route('faculty.create') }}" class="btn btn-primary mb-3">Create New Faculty</a>
    
    <form method="GET" action="{{ route('faculty.index') }}" class="mb-3">
        <div class="d-flex">
            <select name="course_id" id="course_id" class="form-control me-2">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </div>
    </form>
    
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>ID Number</th>
                <th>Course</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faculties as $faculty)
                <tr>
                    <td>
                        @if($faculty->profile)
                            {{ $faculty->profile->first_name }} {{ $faculty->profile->last_name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $faculty->email }}</td>
                    <td>
                        @if($faculty->profile)
                            {{ $faculty->profile->id_number }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $faculty->course ? $faculty->course->course_name : 'N/A' }}</td>
                    <td>
                        {{ $faculty->status_id == 1 ? 'Active' : 'Inactive' }}
                    </td>
                    <td>
                        @if($faculty->status_id == 1)
                            <a href="{{ route('faculty.show', $faculty->id) }}" class="btn btn-info">View</a>
                            <a href="{{ route('faculty.edit', $faculty) }}" class="btn btn-warning">Edit</a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $faculty->id }}">
                                Deactivate
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="deactivateModal-{{ $faculty->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deactivateModalLabel">Deactivate Faculty Account</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to deactivate this faculty account?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('faculty.destroy', $faculty) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Deactivate</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('faculty.reactivate', $faculty) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Reactivate</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
