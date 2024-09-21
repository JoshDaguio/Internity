@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Create Admin Account</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Admin</li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Admin Account Form</h5>

        <!-- Floating Labels Form -->
        <form action="{{ route('admin-accounts.store') }}" method="POST" class="row g-3">
            @csrf

            <!-- First Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingFirstName" name="first_name" placeholder="First Name" required>
                    <label for="floatingFirstName">First Name</label>
                </div>
            </div>

            <!-- Last Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingLastName" name="last_name" placeholder="Last Name" required>
                    <label for="floatingLastName">Last Name</label>
                </div>
            </div>

            <!-- ID Number -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingIDNumber" name="id_number" placeholder="ID Number" required>
                    <label for="floatingIDNumber">ID Number</label>
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="email" class="form-control" id="floatingEmail" name="email" placeholder="Email" required>
                    <label for="floatingEmail">Email</label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('admin-accounts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
