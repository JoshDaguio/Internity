@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Pullout Requests Respond</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Requests</li>
            <li class="breadcrumb-item active">Respond</li>
        </ol>
    </nav>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Respond to Pullout Request</h5>

        <!-- Display details in the specified order -->
        <div class="mb-3">
            <strong>Excuse Detail:</strong>
            <p>{{ $pullout->excuse_detail }}</p>
        </div>

        <div class="mb-3">
            <strong>Sent by:</strong>
            <p>{{ $pullout->creator->profile->first_name }} {{ $pullout->creator->profile->last_name }}</p>
        </div>

        <div class="mb-3">
            <strong>Pullout Date:</strong>
            <p>{{ \Carbon\Carbon::parse($pullout->pullout_date)->format('F d, Y') }}</p>
        </div>

        <div class="mb-3">
            <strong>Student(s) to be pulled out:</strong>
            <ul>
                @foreach ($pullout->students as $student)
                    <li>{{ $student->profile->first_name }} {{ $student->profile->last_name }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Add form for responding to the request -->
        <form action="{{ route('pullouts.respond', $pullout) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="status" class="form-label"><strong>Status</strong></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="accepted">Accept</option>
                    <option value="rejected">Reject</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="company_remark" class="form-label"><strong>Remark</strong></label>
                <textarea name="company_remark" id="company_remark" class="form-control" rows="4" placeholder="Add any comments or remarks..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Submit Response</button>
            <a href="{{ route('pullouts.companyIndex') }}" class="btn btn-secondary btn-sm">Cancel</a>
        </form>
    </div>
</div>
@endsection
