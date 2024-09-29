@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Edit File Upload</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Files</li>
            <li class="breadcrumb-item active">Edit</li>
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
        <h5 class="card-title">Update File Information</h5>

        <form action="{{ route('file_uploads.update', $file->id) }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf
            @method('PATCH')

            <!-- Description -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" name="description" value="{{ $file->description }}" placeholder="Description" required>
                    <label for="description">Description</label>
                </div>
            </div>

            <!-- File Upload (Optional) -->
            <div class="col-md-12">
                <input type="file" class="form-control" name="file" placeholder="Upload New File (optional)">
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('file_uploads.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
