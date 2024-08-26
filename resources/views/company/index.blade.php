@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Company List</h1>
    <a href="{{ route('company.create') }}" class="btn btn-primary">Add New Company</a>
    
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->email }}</td>
                    <td>
                        <a href="{{ route('company.show', $company->id) }}" class="btn btn-info">View</a>
                        <a href="{{ route('company.edit', $company->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('company.destroy', $company->id) }}" method="POST" style="display:inline-block;">
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
