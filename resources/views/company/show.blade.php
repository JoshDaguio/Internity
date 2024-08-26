@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Company Details</h1>

    <table class="table table-bordered">
        <tr>
            <th>Company Name:</th>
            <td>{{ $company->name }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $company->email }}</td>
        </tr>
        <tr>
            <th>Contact Person:</th>
            <td>{{ $company->profile->first_name }} {{ $company->profile->last_name }}</td>
        </tr>
    </table>

    <a href="{{ route('company.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('company.edit', $company->id) }}" class="btn btn-warning">Edit</a>
    <form action="{{ route('company.destroy', $company->id) }}" method="POST" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this company?');">Delete</button>
    </form>
</div>
@endsection
