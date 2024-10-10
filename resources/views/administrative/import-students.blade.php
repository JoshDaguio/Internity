@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Import Student Accounts</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Student</li>
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


    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Upload Students File</h5>

            <form action="{{ route('students.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="file" class="form-label">Select Excel file:</label>
                    <input class="form-control" type="file" id="file" name="file" required>
                    <div class="form-text">
                        Please use the format provided in the template. You can <a href="{{ route('students.template') }}">download the template here</a>.
                        <br><br>
                        <strong>Instructions for entering data:</strong>
                        <ul>
                            <li><strong>Name</strong>: Enter in the format <code>Lastname, Firstname</code>. For example: <code>Doe, John</code>.</li>
                            <li><strong>Email</strong>: Use the official AUF email format, ending in <code>@auf.edu.ph</code>. For example: <code>doe.john@auf.edu.ph</code>.</li>
                            <li><strong>ID Number</strong>: Follow the format <code>00-0000-000</code>. Example: <code>12-3456-789</code>.</li>
                            <li><strong>Course</strong>: Enter the course code (e.g., <code>BSIT</code>) or the full course name (e.g., <code>Bachelor of Science in Information Technology</code>).
                                The system accepts both formats and is case-insensitive, so <code>bsit</code> and <code>BSIT</code> will be recognized as the same course.
                            </li>
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
