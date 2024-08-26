@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Faculty Details</h1>

    <table class="table table-bordered">
        <tr>
            <th>Name:</th>
            <td>{{ $faculty->name }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $faculty->email }}</td>
        </tr>
        <tr>
            <th>Course:</th>
            <td>
            @if ($faculty->course)
                {{ $faculty->course->course_name }}
            @else
                <em>No course assigned</em>
            @endif
            </td>
        </tr>
    </table>

    <a href="{{ route('faculty.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('faculty.edit', $faculty->id) }}" class="btn btn-warning">Edit</a>
    <form action="{{ route('faculty.destroy', $faculty->id) }}" method="POST" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this faculty?');">Delete</button>
    </form>
</div>
@endsection
