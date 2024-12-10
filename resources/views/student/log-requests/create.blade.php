@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Create Log Request</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="{{ route('log-requests.index') }}">Log Requests</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Submit a Log Request</h5>

        <form action="{{ route('log-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="date_request" class="form-label">Date Requested</label>
                <input type="date" name="date_request" id="date_request" class="form-control @error('date_request') is-invalid @enderror" value="{{ old('date_request') }}" required>
                @error('date_request')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="details" class="form-label">Details</label>
                <textarea name="details" id="details" class="form-control @error('details') is-invalid @enderror" rows="3" required>{{ old('details') }}</textarea>
                @error('details')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="proof_file" class="form-label">Proof File (Max: 2MB)</label>
                <input type="file" name="proof_file" id="proof_file" class="form-control @error('proof_file') is-invalid @enderror" required>
                @error('proof_file')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('log-requests.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
