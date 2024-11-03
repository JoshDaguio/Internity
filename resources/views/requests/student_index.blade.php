@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Excusal Requests</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Requests</li>
            </ol>
        </nav>
    </div>

    <!-- Flash message for success -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('requests.create') }}" class="btn btn-success btn-sm mb-3">Create Request</a>
    
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Requests</h5>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Excuse Detail</th>
                                <th>Date of Absence</th>
                                <th>Attached File</th>
                                <th>Status</th>
                                <th>View Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $request)
                                <tr>
                                    <td>{{ $request->subject }}</td>
                                    <td>{{ $request->reason }}</td>
                                    <td>{{ \Carbon\Carbon::parse($request->absence_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if ($request->attachment_path)
                                            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#attachmentModal">
                                                <i class="bi bi-folder"></i>
                                            </button>
                                        @else
                                            <p><code>No file attached</code></p>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($request->status) }}</span></td>
                                    <td><a href="{{ route('requests.studentShow', $request->id) }}" class="btn btn-primary btn-sm">View</a></td>
                                </tr>


                                <!-- Modal for Attachment -->
                                <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="attachmentModalLabel">Attachment Preview</h5>
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
