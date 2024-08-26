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
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $company->name) }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $company->email) }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password (leave blank to keep current password)</label>
            <input type="password" name="password" class="form-control" id="password">   
        </div>

        <button type="submit" class="btn btn-primary">Update Faculty</button>   
    
    </form>

    @endsection