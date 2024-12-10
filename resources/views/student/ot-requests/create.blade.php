@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Create Overtime Requests</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Requests</li>
            <li class="breadcrumb-item active">Create Overtime Requests</li>
        </ol>
    </nav>
</div>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Submit an OT Request</h5>

        <form action="{{ route('ot-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="date_request" class="form-label">Date Requested</label>
                <input type="date" name="date_request" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="ot_start_time" class="form-label">OT Start Time</label>
                <input type="time" name="ot_start_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="ot_end_time" class="form-label">OT End Time</label>
                <input type="time" name="ot_end_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="details" class="form-label">Details</label>
                <textarea name="details" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="proof_file" class="form-label">Proof File</label>
                <input type="file" name="proof_file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

@endsection
