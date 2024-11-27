@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Edit Company Account</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Company</li>
            <li class="breadcrumb-item active">Edit</li>
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
        <h5 class="card-title">Edit Company Account Form</h5>

        <form action="{{ route('company.update', $company->id) }}" method="POST" class="row g-3" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <!-- Company Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" name="name" class="form-control" id="floatingCompanyName" value="{{ old('name', $company->name) }}" placeholder="Company Name" required>
                    <label for="floatingCompanyName">Company Name</label>
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="email" name="email" class="form-control" id="floatingEmail" value="{{ old('email', $company->email) }}" placeholder="Email" required>
                    <label for="floatingEmail">Email</label>
                </div>
            </div>

            <!-- Contact Person First Name -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="first_name" class="form-control" id="floatingFirstName" value="{{ old('first_name', $company->profile->first_name) }}" placeholder="First Name" required>
                    <label for="floatingFirstName">Contact Person First Name</label>
                </div>
            </div>

            <!-- Contact Person Last Name -->
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="last_name" class="form-control" id="floatingLastName" value="{{ old('last_name', $company->profile->last_name) }}" placeholder="Last Name" required>
                    <label for="floatingLastName">Contact Person Last Name</label>
                </div>
            </div>

            <!-- Contact Number -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" name="contact_number" class="form-control" id="floatingContactNumber" 
                        value="{{ old('contact_number', $company->profile->contact_number) }}" 
                        placeholder="Contact Number">
                    <label for="floatingContactNumber">Contact Number</label>
                </div>
            </div>


            <!-- MOA File Upload -->
            <div class="col-md-12">
                <label for="moa_file" class="form-label">Upload MOA (Leave blank to keep current file)</label>
                <input type="file" name="moa_file" class="form-control" id="moa_file" accept=".pdf,.doc,.docx">
                @if($company->profile->moa_file_path)
                    <p class="mt-2">Current MOA: 
                        <a href="{{ asset('storage/' . $company->profile->moa_file_path) }}" target="_blank">View File</a>
                    </p>
                @endif
            </div>

            <!-- Password (Optional) -->
            <div class="col-12">
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password (Leave blank to keep current password)</label>
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
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('company.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
