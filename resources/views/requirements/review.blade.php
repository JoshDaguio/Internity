@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Review Internship Requirements</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Student</li>
            <li class="breadcrumb-item">{{ $requirements->student->profile->first_name }} {{ $requirements->student->profile->last_name }}</li>
            <li class="breadcrumb-item active">Requirements</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- Student Details -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Student Details</h5>
        <p><strong>Name:</strong> {{ $requirements->student->profile->first_name }} {{ $requirements->student->profile->last_name }}</p>
        <p><strong>ID Number:</strong> {{ $requirements->student->profile->id_number }}</p>
        <p><strong>Course:</strong> {{ $requirements->student->course->course_code }}</p>
    </div>
</div>

<!-- Step 1: Waiver Form and Medical Certificate -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Step 1: Review Submissions</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Requirements</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Waiver Form -->
                <tr>
                    <td>Waiver Form</td>
                    <td>
                        @if($requirements->waiver_status_id == 1)
                            <span class="badge bg-warning">To Review</span>
                        @elseif($requirements->waiver_status_id == 2)
                            <span class="badge bg-success">Accepted</span>
                        @elseif($requirements->waiver_status_id == 3)
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>
                        @if($requirements->waiver_form)
                            <button class="btn btn-dark" onclick="showPreview('{{ route('preview.requirement', ['type' => 'waiver', 'id' => $requirements->id]) }}')">
                                <i class="bi bi-folder"></i>
                            </button>
                            @if($requirements->waiver_status_id == 1) <!-- Hide Accept/Reject buttons if Accepted/Rejected -->
                                <form action="{{ route('admin.accept.waiver', $requirements->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>
                                <form action="{{ route('admin.reject.waiver', $requirements->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>

                <!-- Medical Certificate -->
                <tr>
                    <td>Medical Certificate</td>
                    <td>
                        @if($requirements->medical_status_id == 1)
                            <span class="badge bg-warning">To Review</span>
                        @elseif($requirements->medical_status_id == 2)
                            <span class="badge bg-success">Accepted</span>
                        @elseif($requirements->medical_status_id == 3)
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>
                        @if($requirements->medical_certificate)
                            <button class="btn btn-dark" onclick="showPreview('{{ route('preview.requirement', ['type' => 'medical', 'id' => $requirements->id]) }}')">
                                <i class="bi bi-folder"></i>
                            </button>
                            @if($requirements->medical_status_id == 1) <!-- Hide Accept/Reject buttons if Accepted/Rejected -->
                                <form action="{{ route('admin.accept.medical', $requirements->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>
                                <form action="{{ route('admin.reject.medical', $requirements->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </form>
                            @endif
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Step 2: Endorsement Letter -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Step 2: Endorsement Letter</h5>
        @if($requirements->endorsement_letter)
            <div class="mb-3">
                <label for="endorsement_letter" class="form-label">View Uploaded Endorsement Letter: </label>
                <button class="btn btn-dark" onclick="showPreview('{{ route('preview.requirement', ['type' => 'endorsement', 'id' => $requirements->id]) }}')">
                    <i class="bi bi-folder"></i> 
                </button>
            </div>
        @endif
        <form action="{{ route('admin.upload.endorsement', $requirements->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="endorsement_letter" class="form-label">Upload Endorsement Letter</label>
                <input type="file" class="form-control" name="endorsement_letter" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
</div>

<!-- Modal for file preview -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewLabel">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="filePreviewIframe" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    function showPreview(url) {
        document.getElementById('filePreviewIframe').src = url;
        var filePreviewModal = new bootstrap.Modal(document.getElementById('filePreviewModal'), {});
        filePreviewModal.show();
    }
</script>

@endsection
