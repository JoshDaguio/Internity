@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Edit Company</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('company.update', $company->id) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label for="name" class="form-label">Company Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $company->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $company->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="first_name" class="form-label">Contact Person First Name</label>
            <input type="text" name="first_name" class="form-control" id="first_name" value="{{ old('first_name', $company->profile->first_name) }}" required>
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Contact Person Last Name</label>
            <input type="text" name="last_name" class="form-control" id="last_name" value="{{ old('last_name', $company->profile->last_name) }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password (leave blank to keep current password)</label>
            <input type="password" name="password" class="form-control" id="password">
        </div>

        <button type="submit" class="btn btn-primary">Update Company</button>
        <a href="{{ route('company.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
