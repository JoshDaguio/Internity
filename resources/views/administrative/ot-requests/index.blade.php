@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>OT Requests</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Request</a></li>
            <li class="breadcrumb-item active">Overtime Requests</li>
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
        <h5 class="card-title">Pending OT Requests</h5>
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Date Requested</th>
                    <th>OT Start</th>
                    <th>OT End</th>
                    <th>Details</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($otRequests as $otRequest)
                <tr>
                    <td>{{ $otRequest->student->profile->first_name }} {{ $otRequest->student->profile->last_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($otRequest->date_request)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($otRequest->ot_start_time)->format('h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($otRequest->ot_end_time)->format('h:i A') }}</td>
                    <td>{{ $otRequest->details }}</td>
                    <td>
                        <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#proofModal-{{ $otRequest->id }}">
                            <i class="bi bi-folder"></i>
                        </button>
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
                    <td>
                        @if($otRequest->status === 'pending')
                            <!-- Approve Button -->
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal-{{ $otRequest->id }}">
                                Approve
                            </button>

                            <!-- Reject Button -->
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $otRequest->id }}">
                                Reject
                            </button>
                        @elseif($otRequest->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($otRequest->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                </tr>

                <!-- Proof Modal -->
                <div class="modal fade" id="proofModal-{{ $otRequest->id }}" tabindex="-1" aria-labelledby="proofModalLabel-{{ $otRequest->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="proofModalLabel-{{ $otRequest->id }}">Proof for OT Request ({{ \Carbon\Carbon::parse($otRequest->date_request)->format('F d, Y') }})</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if(pathinfo($otRequest->proof_file_path, PATHINFO_EXTENSION) === 'pdf')
                                    <iframe src="{{ asset('storage/' . $otRequest->proof_file_path) }}" style="width:100%; height:500px;" frameborder="0"></iframe>
                                @else
                                    <img src="{{ asset('storage/' . $otRequest->proof_file_path) }}" alt="Proof" class="img-fluid">
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approve Modal -->
                <div class="modal fade" id="approveModal-{{ $otRequest->id }}" tabindex="-1" aria-labelledby="approveModalLabel-{{ $otRequest->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.ot-requests.approve', $otRequest->id) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="approveModalLabel-{{ $otRequest->id }}">Approve OT Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to approve this OT request?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal-{{ $otRequest->id }}" tabindex="-1" aria-labelledby="rejectModalLabel-{{ $otRequest->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.ot-requests.reject', $otRequest->id) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rejectModalLabel-{{ $otRequest->id }}">Reject OT Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="remarks-{{ $otRequest->id }}" class="form-label">Remarks</label>
                                    <textarea name="remarks" id="remarks-{{ $otRequest->id }}" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
