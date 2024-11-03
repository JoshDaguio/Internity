@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Excusal Requests</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Requests</li>
                <li class="breadcrumb-item active">Student Request</li>
            </ol>
        </nav>
    </div>

    
    <!-- Flash message for success -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif



    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Filter by Academic Year and Status</h5>
            <!-- Academic Year and Status Filter -->
            <form method="GET" action="{{ route('requests.adminIndex') }}">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="academic_year_id" class="form-label">Academic Year</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                    {{ $year->start_year }} - {{ $year->end_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Request Status</label>
                        <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                            <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Requests</h5>
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Excuse Detail</th>
                            <th>Submitted By</th>
                            <th>Date of Absence</th>
                            <th>Attached File</th>
                            <th>Status</th>
                            <th>Respond</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                            <tr>
                                <td>{{ $request->subject }}</td>
                                <td>{{ $request->reason }}</td>
                                <td>{{ $request->student->profile->first_name }} {{ $request->student->profile->last_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($request->absence_date)->format('M d, Y') }}</td>
                                <td>
                                    @if ($request->attachment_path)
                                        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#attachmentModal-{{ $request->id }}">
                                            <i class="bi bi-folder"></i>
                                        </button>
                                    @else
                                        <p><code>No file attached</code></p>
                                    @endif
                                </td>
                                <td><span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($request->status) }}</span></td>
                                <td><a href="{{ route('requests.show', $request->id) }}" class="btn btn-primary btn-sm">View</a></td>
                            </tr>

                            <!-- Modal for Attachment Preview for each request -->
                            <div class="modal fade" id="attachmentModal-{{ $request->id }}" tabindex="-1" aria-labelledby="attachmentModalLabel-{{ $request->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="attachmentModalLabel-{{ $request->id }}">Attachment Preview</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <iframe src="{{ route('requests.preview', $request->id) }}" style="width:100%; height:600px;" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>
    function showPreview(url) {
        const modal = new bootstrap.Modal(document.getElementById('attachmentModal'), {});
        document.querySelector('#attachmentModal iframe').src = url;
        modal.show();
    }
</script>
@endsection
