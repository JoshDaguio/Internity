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

        <!-- Floating Labels Form with Validation -->
        <form action="{{ route('admin-accounts.store') }}" method="POST" class="row g-3 needs-validation" novalidate>
            @csrf

            <!-- First Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingFirstName" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required>
                    <label for="floatingFirstName">First Name</label>
                    <div class="invalid-feedback">Please enter the first name.</div>
                </div>
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>

            <!-- Last Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingLastName" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
                    <label for="floatingLastName">Last Name</label>
                    <div class="invalid-feedback">Please enter the last name.</div>
                </div>
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>

            <!-- ID Number -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingIDNumber" name="id_number" placeholder="ID Number" value="{{ old('id_number') }}" required>
                    <label for="floatingIDNumber">ID Number</label>
                    <div class="invalid-feedback">Please enter a valid ID number.</div>
                </div>
                <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="email" class="form-control" id="floatingEmail" name="email" placeholder="Email" value="{{ old('email') }}" required>
                    <label for="floatingEmail">Email</label>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

<script>
    // JavaScript for Bootstrap validation
    (function () {
        'use strict'

        const forms = document.querySelectorAll('.needs-validation')

        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
