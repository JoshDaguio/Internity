@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Overtime Requests</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Requests</li>
            <li class="breadcrumb-item active">Overtime Requests</li>
        </ol>
    </nav>
</div>

<a href="{{ route('ot-requests.create') }}" class="btn btn-primary btn-sm mb-3">Create</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">OT Requests</h5>
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Date Requested</th>
                    <th>OT Start</th>
                    <th>OT End</th>
                    <th>Details</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($otRequests as $otRequest)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($otRequest->date_request)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($otRequest->ot_start_time)->format('h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($otRequest->ot_end_time)->format('h:i A') }}</td>
                    <td>{{ $otRequest->details }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $otRequest->proof_file_path) }}" target="_blank" class="btn btn-dark btn-sm">
                            <i class="bi bi-folder"></i>
                        </a>
                    </td>
                    <td>
                        @if($otRequest->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($otRequest->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($otRequest->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>{{ $otRequest->remarks ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
