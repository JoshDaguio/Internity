@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>My Log Requests</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Requests</li>
            <li class="breadcrumb-item active">Log Requests</li>
        </ol>
    </nav>
</div>

<a href="{{ route('log-requests.create') }}" class="btn btn-primary btn-sm mb-3">Create</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


<div class="card">
    <div class="card-body">
        <h5 class="card-title">Log Requests</h5>
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Date Sent</th>
                    <th>Date Requested</th>
                    <th>Details</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logRequests as $logRequest)
                <tr>
                    <td>{{ $logRequest->created_at->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($logRequest->date_request)->format('M d, Y') }}</td>
                    <td>{{ $logRequest->details }}</td>
                    <td>
                        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#proofModal-{{ $logRequest->id }}">
                            <i class="bi bi-folder"></i>
                        </button>
                    </td>
                    <td>
                        @if($logRequest->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($logRequest->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($logRequest->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>{{ $logRequest->remarks ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modals for Proof Previews -->
@foreach($logRequests as $logRequest)
    <div class="modal fade" id="proofModal-{{ $logRequest->id }}" tabindex="-1" aria-labelledby="proofModalLabel-{{ $logRequest->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proofModalLabel-{{ $logRequest->id }}">Proof for Log Request ({{ \Carbon\Carbon::parse($logRequest->date_request)->format('F d, Y') }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if(pathinfo($logRequest->proof_file_path, PATHINFO_EXTENSION) == 'pdf')
                        <iframe src="{{ asset('storage/' . $logRequest->proof_file_path) }}" style="width:100%; height:500px;" frameborder="0"></iframe>
                    @else
                        <img src="{{ asset('storage/' . $logRequest->proof_file_path) }}" alt="Proof" class="img-fluid">
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection
