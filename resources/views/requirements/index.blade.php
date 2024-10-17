@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Internship Requirements</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Requirements</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- Internship Requirement Instructions -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Internship Requirement Instructions</h5>

        <div class="row">
            <!-- Step 1 -->
            <div class="col-md-6">
                <h6 class="card-title">Step 1</h6>
                <ul>
                    <li>Submit <strong>Waiver Form</strong> (See <strong>Files</strong> Section)</li>
                    <li>Submit <strong>Medical Certificate</strong></li>
                    <!-- <li>Consent (If Required by PhilHealth)</li> -->
                </ul>
            </div>

            <!-- Step 2 -->
            <div class="col-md-6">
                <h6 class="card-title">Step 2</h6>
                <ul>
                    <li><strong>Pre-requisite:</strong> Accomplish Step 1 Requirements</li>
                    <li>Endorsement Letter/s</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Step 1 Submission Section -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Step 1</h5>

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
                        @if($requirements->waiver_form)
                            @if($requirements->waiver_status_id == 1)
                                <span class="badge bg-warning">To Review</span>
                            @elseif($requirements->waiver_status_id == 2)
                                <span class="badge bg-success">Accepted</span>
                            @elseif($requirements->waiver_status_id == 3)
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">Not Submitted</span>
                        @endif
                    </td>
                    <td>
                        @if($requirements->waiver_status_id == 3 || !$requirements->waiver_form)
                            <!-- Re-upload Waiver Form if Rejected or Not Submitted -->
                            <form action="{{ route('submit.waiver') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex align-items-center">
                                    <input type="file" name="waiver_form" class="form-control me-2" required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-upload"></i>
                                    </button>
                                </div>
                            </form>
                        @elseif($requirements->waiver_form)
                            <button class="btn btn-dark" onclick="showPreview('{{ route('preview.requirement', ['type' => 'waiver', 'id' => $requirements->id]) }}')">
                                <i class="bi bi-folder"></i>
                            </button>
                        @endif
                    </td>
                </tr>

                <!-- Medical Certificate -->
                <tr>
                    <td>Medical Certificate</td>
                    <td>
                        @if($requirements->medical_certificate)
                            @if($requirements->medical_status_id == 1)
                                <span class="badge bg-warning">To Review</span>
                            @elseif($requirements->medical_status_id == 2)
                                <span class="badge bg-success">Approved</span>
                            @elseif($requirements->medical_status_id == 3)
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">Not Submitted</span>
                        @endif
                    </td>
                    <td>
                        @if($requirements->medical_status_id == 3 || !$requirements->medical_certificate)
                            <!-- Re-upload Medical Certificate if Rejected or Not Submitted -->
                            <form action="{{ route('submit.medical') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex align-items-center">
                                    <input type="file" name="medical_certificate" class="form-control me-2" required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-upload"></i>
                                    </button>
                                </div>
                            </form>
                        @elseif($requirements->medical_certificate)
                            <button class="btn btn-dark" onclick="showPreview('{{ route('preview.requirement', ['type' => 'medical', 'id' => $requirements->id]) }}')">
                                <i class="bi bi-folder"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Step 2 Section (Locked if Step 1 is not completed) -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Step 2</h5>
        @if(!$step1Completed)
            <p class="text-center text-muted"><b>Please Accomplish Step 1 Before Proceeding.</b></p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Requirement</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Endorsement Letter -->
                    <tr>
                        <td>Endorsement Letter</td>
                        <td>
                            @if($requirements->endorsement_letter)
                                <span class="badge bg-success">Uploaded</span>
                            @else
                                <span class="badge bg-secondary">Not Available</span>
                            @endif
                        </td>
                        <td>
                            @if($requirements->endorsement_letter)
                                <!-- Preview Button -->
                                <button class="btn btn-dark" onclick="showPreview('{{ route('preview.requirement', ['type' => 'endorsement', 'id' => $requirements->id]) }}')">
                                    <i class="bi bi-folder"></i> 
                                </button>
                            @else
                                <p class="text-muted">Endorsement Letter not uploaded yet.</p>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
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
