@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Internship Files - {{ $student->profile->first_name }} {{ $student->profile->last_name }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Students</li>
            <li class="breadcrumb-item">{{ $student->profile->first_name }} {{ $student->profile->last_name }}</li>
            <li class="breadcrumb-item active">Internship Files</li>
        </ol>
    </nav>
</div>

<a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary mb-3 btn-sm">Back</a>


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
                            <p><strong><code>No Endorsement Letter uploaded yet.</code></strong></p>
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
                            <p><strong><code>Waiver either not approved yet or not uploaded by student.</code></strong></p>
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
                            <p><strong><code>No Resume uploaded by student in their profile yet.</code></strong></p>
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
                            <p><strong><code>Medical Certificate either not approved yet or not uploaded by student.</code></strong></p>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <hr class="my-4">

        <!-- Monthly Reports Section -->
        <h5 class="card-title">Monthly Reports</h5>
        @if($hasAcceptedInternship)
            <h6><strong>Submitted Monthly EOD Reports:</strong></h6>
            <ul>
                @if($eodReports->isNotEmpty())
                    @foreach($eodReports as $report)
                        <li>
                            {{ \Carbon\Carbon::parse($report->month_year)->format('F Y') }}
                            <button onclick="showPreview('{{ route('admin.internship.files.monthly.preview', ['type' => 'eod', 'id' => $report->id, 'studentId' => $student->id]) }}')" class="btn btn-dark btn-sm">
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
                            <button onclick="showPreview('{{ route('admin.internship.files.monthly.preview', ['type' => 'dtr', 'id' => $report->id, 'studentId' => $student->id]) }}')" class="btn btn-dark btn-sm">
                                <i class="bi bi-folder"></i>
                            </button>
                        </li>
                    @endforeach
                @else
                    <p><code><strong>No Monthly DTR Report submitted yet.</strong></code></p>
                @endif
            </ul>
        @else
            <p><strong><code>Student has no accepted internship yet.</code></strong></p>
        @endif
        <hr class="my-4">

        <!-- Completion Requirements Section -->
        <h5 class="card-title">Completion Requirements</h5>
        @if($hasAcceptedInternship)
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Preview</th>
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
                                @else
                                    <p><strong><code>Student has no submission yet</code></strong></p>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p><strong><code>Student has no accepted internship yet.</code></strong></p>
        @endif
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
