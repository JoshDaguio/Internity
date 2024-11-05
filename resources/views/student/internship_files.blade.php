@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Files Required for Internship Completion</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Internship Files</li>
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

        @if($hasAcceptedInternship)
            <h5 class="card-title">Internship Dates</h5>
            <p>Start Date: <span class="badge bg-success">{{ $startDate ? $startDate->format('F d, Y') : 'Not Available' }}</span></p>
            <p>Estimated Finish Date: <span class="badge bg-primary">{{ $estimatedFinishDate ? $estimatedFinishDate->format('F d, Y') : 'Not Available' }}</span></p>
        @endif

        <h5 class="card-title">Initial Requirements</h5>
        <!-- Individual File Links -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Preview</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Endorsement Letter</td>
                    <td>
                        @if($files['endorsement_letter'])
                        <button class="btn btn-dark btn-sm" onclick="showPreview('{{ route('preview.requirement', ['type' => 'endorsement', 'id' => $requirements->id]) }}')">
                            <i class="bi bi-folder"></i>
                        </button>
                        @else 
                            <p><strong><code>Please wait for your Endorsement Letter to be uploaded.</code></strong></p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Waiver</td>
                    <td>
                        @if($files['waiver'])
                            <button class="btn btn-dark btn-sm" onclick="showPreview('{{ route('preview.requirement', ['type' => 'waiver', 'id' => $requirements->id]) }}')">
                                <i class="bi bi-folder"></i>
                            </button>
                        @else
                            <p><strong><code>Please wait for your Waiver to be approved.</code></strong></p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Resume</td>
                    <td>
                        @if($files['resume'])
                            <button class="btn btn-dark btn-sm" onclick="showPreview('{{ route('profile.previewCV', $student->profile->id) }}')">
                                <i class="bi bi-folder"></i>
                            </button>
                        @else 
                            <p><strong><code>Please upload your Resume/CV on your profile</code></strong></p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Medical Certificate</td>
                    <td>
                        @if($files['medical_certificate'])
                            <button class="btn btn-dark btn-sm" onclick="showPreview('{{ route('preview.requirement', ['type' => 'medical', 'id' => $requirements->id]) }}')">
                                <i class="bi bi-folder"></i>
                            </button>
                        @else
                            <p><strong><code>Please wait for your Medical Certificate to be approved.</code></strong></p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="form-text">
            <p><strong><code>Note: The files above are linked from your profile and requirements</code></strong></p>
        </div>

        <hr class="my-4">
        <!-- Monthly Reports Upload Section -->
        <div class="mt-4">
            <h5 class="card-title">Monthly Reports</h5>
            @if($hasAcceptedInternship)
                <div class="mt-3">
                    <h6><strong>Submitted Monthly EOD Reports:</strong></h6>
                    <ul>
                        @if($eodReports->isNotEmpty())
                            @foreach($eodReports as $report)
                                <li>
                                    {{ \Carbon\Carbon::parse($report->month_year)->format('F Y') }}
                                    <button onclick="showPreview('{{ route('internship.files.monthly.preview', ['type' => 'eod', 'id' => $report->id]) }}')" class="btn btn-dark btn-sm">
                                        <i class="bi bi-folder"></i>
                                    </button>
                                </li>
                            @endforeach
                        @else
                            <p><code><strong>No Monthly EOD Report submitted yet.</strong></code></p>
                        @endif
                    </ul>

                    <h6><strong>Submitted Monthly DTR Reports:</strong></h6>
                    <ul>
                        @if($dtrReports->isNotEmpty())
                            @foreach($dtrReports as $report)
                                <li>
                                    {{ \Carbon\Carbon::parse($report->month_year)->format('F Y') }}
                                    <button onclick="showPreview('{{ route('internship.files.monthly.preview', ['type' => 'dtr', 'id' => $report->id]) }}')" class="btn btn-dark btn-sm">
                                        <i class="bi bi-folder"></i>
                                    </button>
                                </li>
                            @endforeach
                        @else
                            <p><code><strong>No Monthly DTR Report submitted yet.</strong></code></p>
                        @endif
                    </ul>
                </div>
            @else
                <p><strong><code>You need an accepted internship to view Monthly Reports.</code></strong></p>
            @endif
        </div>

        @if($hasAcceptedInternship)
        <div class="form-text">
            <p><strong><code>Note: Monthly EODs and DTRs are automatically linked once you generate and downloaded their monthtly reports</code></strong></p>
        </div>
        @endif

        <hr class="my-4">

        <!-- Completion Requirements Section -->
        <h5 class="card-title">Completion Requirements</h5>
        @if($hasAcceptedInternship)
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Preview | Upload</th>
                        <!-- <th>Note</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach(['intern_evaluation' => 'Intern Evaluation Form', 'certificate_completion' => 'Certificate of Completion', 'exit_form' => 'Intern’s Exit Form'] as $type => $label)
                        <tr>
                            <td>{{ $label }}</td>
                            <td>
                                @if($files[$type])
                                    <button class="btn btn-dark btn-sm" onclick="showPreview('{{ route('internship.files.completion.preview', ['type' => $type, 'id' => $completionReqs->id]) }}')">
                                        <i class="bi bi-folder"></i>
                                    </button>
                                    <!-- Re-upload Form -->
                                    <form action="{{ route('upload.completion.file', $type) }}" method="POST" enctype="multipart/form-data" class="d-inline-block ms-2">
                                        @csrf
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="file" class="form-control me-2" required>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-upload"></i>
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <!-- Initial Upload Form -->
                                    <form action="{{ route('upload.completion.file', $type) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="d-flex align-items-center">
                                            <input type="file" name="file" class="form-control me-2" required>
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="bi bi-upload"></i>
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            <div class="form-text">
                <p><strong><code>Note: The files above should be uploaded only after the student has completed their internship, received the Certificate of Completion, and finished the required evaluations.</code></strong></p>
            </div>
        @else
            <p><strong><code>You need an accepted internship to upload Completion Requirements.</code></strong></p>
        @endif


        <hr class="my-4">


        <!-- Download All Files Button -->
        <div class="text-center mt-4">
            <a href="{{ route('download.all.files') }}" 
            class="btn btn-success btn-sm {{ $hasFiles ? '' : 'disabled' }}" 
            {{ $hasFiles ? '' : 'aria-disabled="true"' }}>
                Download All Files
            </a>
        </div>

    </div>
</div>

<!-- Modal for File Preview -->
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
