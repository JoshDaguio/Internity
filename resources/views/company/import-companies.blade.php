@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Import Company Accounts</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Company</li>
            <li class="breadcrumb-item active">Import</li>
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
        <h5 class="card-title">Upload Companies File</h5>

        <form action="{{ route('company.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="file" class="form-label">Select CSV or Excel file:</label>
                <input class="form-control" type="file" id="file" name="file" required>
                <div class="form-text">
                    Please use the format provided in the template. You can <a href="{{ route('company.template') }}">download the template here</a>.
                    <br><br>
                    <strong>Instructions for entering data:</strong>
                    <ul>
                        <li><strong>Company Name</strong>: Enter the full name of the company.</li>
                        <li><strong>Email</strong>: Enter a valid email address.</li>
                        <li><strong>Contact Person</strong>: Enter in the format <code>Lastname, Firstname</code>. For example: <code>Doe, John</code>.</li>
                    </ul>
                    <br>
                    Ensure that all data is filled out correctly according to these formats to avoid errors during the upload process.
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>
@endsection
