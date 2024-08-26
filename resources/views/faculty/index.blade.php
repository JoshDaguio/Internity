@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Faculty List</h1>

    <!-- Row for buttons -->
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('faculty.create') }}" class="btn btn-primary btn-block">Add New Faculty</a>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-secondary btn-block" type="button" data-bs-toggle="collapse" data-bs-target="#filterSection" aria-expanded="false" aria-controls="filterSection">
                Filters & Sorting
            </button>
        </div>
    </div>

    <!-- Filter & Sorting Form (Collapsible Section) -->
    <div class="collapse mt-3" id="filterSection">
        <div class="card card-body">
            <form method="GET" action="{{ route('faculty.index') }}">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="course_id">Filter by Course</label>
                        <select id="course_id" name="course_id" class="form-control">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="sort_by">Sort by</label>
                        <select id="sort_by" name="sort_by" class="form-control">
                            <option value="">Sort By</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                            <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="order">Order</label>
                        <select id="order" name="order" class="form-control">
                            <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                            <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descending</option>
                        </select>
                    </div>
                </div>

                <br>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </form>
        </div>
    </div>

    <table class="table mt-3">
        <thead>
            <tr>
                <th><a href="{{ route('faculty.index', array_merge(request()->query(), ['sort_by' => 'name', 'order' => request('order') == 'asc' ? 'desc' : 'asc'])) }}">Name</a></th>
                <th><a href="{{ route('faculty.index', array_merge(request()->query(), ['sort_by' => 'email', 'order' => request('order') == 'asc' ? 'desc' : 'asc'])) }}">Email</a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faculties as $faculty)
                <tr>
                    <td>{{ $faculty->name }}</td>
                    <td>{{ $faculty->email }}</td>
                    <td>
                        <a href="{{ route('faculty.show', $faculty->id) }}" class="btn btn-info">View</a>
                        <a href="{{ route('faculty.edit', $faculty->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('faculty.destroy', $faculty->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
