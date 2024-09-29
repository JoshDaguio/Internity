@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Upload a New File</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Files</li>
            <li class="breadcrumb-item active">Upload</li>
        </ol>
    </nav>
</div>

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
        <h5 class="card-title">File Upload Form</h5>

        <form action="{{ route('file_uploads.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf

            <!-- File Upload -->
            <div class="col-md-12">
                <input type="file" name="file" id="file" class="form-control" placeholder="Choose File" required>
            </div>

            <!-- Description -->
            <div class="col-md-12">
                <div class="form-floating">
                    <textarea name="description" id="description" class="form-control" placeholder="Description" style="height: 150px;" required></textarea>
                    <label for="description">Description</label>
                </div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Upload</button>
                <a href="{{ route('file_uploads.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
