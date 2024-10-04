@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Create Company Account</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Company</li>
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
        <h5 class="card-title">Create Company Account Form</h5>

        <!-- Floating Labels Form -->
        <form action="{{ route('company.store') }}" method="POST" class="row g-3">
            @csrf

            <!-- Company Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" name="name" class="form-control" id="floatingCompanyName" value="{{ old('name') }}" placeholder="Company Name" required>
                    <label for="floatingCompanyName">Company Name</label>
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="email" name="email" class="form-control" id="floatingEmail" value="{{ old('email') }}" placeholder="Email" required>
                    <label for="floatingEmail">Email</label>
                </div>
            </div>

            <!-- Contact Person First Name -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" id="floatingFirstName" value="{{ old('first_name') }}" placeholder="First Name" required>
                    <label for="floatingFirstName">Contact Person First Name</label>
                </div>
            </div>

            <!-- Contact Person Last Name -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" id="floatingLastName" value="{{ old('last_name') }}" placeholder="Last Name" required>
                    <label for="floatingLastName">Contact Person Last Name</label>
                </div>
            </div>

            <!-- Expiry Date Field in Blade Form -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="date" name="expiry_date" class="form-control" id="expiry_date" value="{{ old('expiry_date', $company->expiry_date ?? '') }}" placeholder="Expiry Date" required min="{{ date('Y-m-d') }}">
                    <label for="expiry_date">Expiry Date</label>
                </div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('company.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
