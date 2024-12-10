@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Log Requests</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Log Requests</li>
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
        <h5 class="card-title">Log Requests</h5>
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Date Requested</th>
                    <th>Details</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logRequests as $logRequest)
                <tr>
                    <td>{{ $logRequest->student->profile->first_name }} {{ $logRequest->student->profile->last_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($logRequest->date_request)->format('M d, Y') }}</td>
                    <td>{{ $logRequest->details }}</td>
                    <td>
                        <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#proofModal-{{ $logRequest->id }}">
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
                    <td>
                        @if($logRequest->status === 'pending')
                            <!-- Accept Button -->
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#acceptModal{{ $logRequest->id }}">
                                Accept
                            </button>

                            <!-- Reject Button -->
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $logRequest->id }}">
                                Reject
                            </button>
                        @elseif($logRequest->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($logRequest->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                </tr>

                <!-- Proof Modal -->
                <div class="modal fade" id="proofModal-{{ $logRequest->id }}" tabindex="-1" aria-labelledby="proofModalLabel-{{ $logRequest->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="proofModalLabel-{{ $logRequest->id }}">Proof for Log Request ({{ \Carbon\Carbon::parse($logRequest->date_request)->format('F d, Y') }})</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if(pathinfo($logRequest->proof_file_path, PATHINFO_EXTENSION) === 'pdf')
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

                <!-- Accept Modal -->
                <div class="modal fade" id="acceptModal{{ $logRequest->id }}" tabindex="-1" aria-labelledby="acceptModalLabel{{ $logRequest->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.logTime', $logRequest->student_id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="logRequestId" value="{{ $logRequest->id }}">
                            <input type="hidden" name="log_date" value="{{ $logRequest->date_request }}">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="acceptModalLabel{{ $logRequest->id }}">Log Time for {{ $logRequest->student->profile->first_name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="morning_in">Morning In:</label>
                                    <input type="time" name="morning_in" id="morning_in" class="form-control">

                                    <label for="morning_out">Morning Out:</label>
                                    <input type="time" name="morning_out" id="morning_out" class="form-control">

                                    <label for="afternoon_in">Afternoon In:</label>
                                    <input type="time" name="afternoon_in" id="afternoon_in" class="form-control">

                                    <label for="afternoon_out">Afternoon Out:</label>
                                    <input type="time" name="afternoon_out" id="afternoon_out" class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $logRequest->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $logRequest->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.log-requests.reject', $logRequest->id) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rejectModalLabel{{ $logRequest->id }}">Reject Log Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="remarks">Remarks:</label>
                                    <textarea name="remarks" id="remarks" class="form-control" required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
