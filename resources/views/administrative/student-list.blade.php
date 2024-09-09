@extends('layouts.app')

@section('body')
    <h1>Student List</h1>

    <a href="{{ route('students.create') }}" class="btn btn-primary mb-3">Add Student</a>


    <!-- Filters -->
    <form method="GET" action="{{ route('students.list') }}" class="mb-3">
        <div class="d-flex">
            <select name="course_id" id="course_id" class="form-select me-2" onchange="this.form.submit()">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }}
                    </option>
                @endforeach
            </select>

            <select name="status_id" id="status_id" class="form-select me-2" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="1" {{ request('status_id') == '1' ? 'selected' : '' }}>Active</option>
                <option value="2" {{ request('status_id') == '2' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($approvedStudents as $student)
                <tr>
                    <td>{{ $student->profile ? $student->profile->id_number : 'N/A' }}</td>
                    <td>{{ $student->profile ? $student->profile->last_name : 'N/A' }}, {{ $student->profile ? $student->profile->first_name : 'N/A' }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->course ? $student->course->course_code : 'N/A' }}</td>
                    <td>{{ $student->status_id == 1 ? 'Active' : 'Inactive' }}</td>
                    <td>
                        @if($student->status_id == 1)
                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-info"><i class="bi bi-info-circle"></i></a>
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $student->id }}">
                                Deactivate
                            </button>

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
                                            <form action="{{ route('students.deactivate', $student->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Deactivate</button>
                                            </form>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('students.reactivate', $student->id) }}" method="POST" style="display:inline;">
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
