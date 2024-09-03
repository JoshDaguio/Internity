@extends('layouts.app')

@section('body')
    <h1>Edit Admin Account</h1>
    <form action="{{ route('admin-accounts.update', $admin) }}" method="POST">
        @csrf
        @method('PATCH')
        
        <!-- First Name -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $admin->profile->first_name ?? '' }}" placeholder="First Name" required>
        </div>
        
        <!-- Last Name -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $admin->profile->last_name ?? '' }}" placeholder="Last Name" required>
        </div>
        
        <!-- ID Number -->
        <div class="mb-3">
            <label for="id_number" class="form-label">ID Number</label>
            <input type="text" class="form-control" id="id_number" name="id_number" value="{{ $admin->profile->id_number ?? '' }}" placeholder="ID Number" required>
        </div>
        
        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $admin->email }}" required>
        </div>
        <!-- Submit Button -->
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin-accounts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
