@extends('layouts.app')

@section('body')
    <h1>Upload a New File</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('file_uploads.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Choose File:</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <div class="form-group mt-3">
            <label for="description">Description:</label>
            <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Upload</button>
    </form>
@endsection
