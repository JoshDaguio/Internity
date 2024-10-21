@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Accepted Interns</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Internships</li>
            <li class="breadcrumb-item active">Interns</li>
        </ol>
    </nav>
</div>

<!-- Accepted Interns List -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Accepted Interns</h5>
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Intern</th>
                        <th>Job</th>
                        <th>Email</th>
                        <th>Start Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($acceptedInterns as $intern)
                    <tr>
                        <td>
                            <a href="{{ route('students.show', $intern->student->id) }}" class="btn btn-light btn-sm">
                                {{ $intern->student->profile->last_name }}, {{ $intern->student->profile->first_name }}
                            </a>
                        </td>
                        <td>{{ $intern->job->title }}</td>
                        <td>{{ $intern->student->email }}</td>
                        <td>{{ \Carbon\Carbon::parse($intern->start_date)->format('F d, Y') }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewInternModal{{ $intern->id }}">
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Intern Details Modal -->
                    <div class="modal fade" id="viewInternModal{{ $intern->id }}" tabindex="-1" aria-labelledby="viewInternModalLabel{{ $intern->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewInternModalLabel{{ $intern->id }}">Intern Information</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Full Name:</strong> {{ $intern->student->profile->first_name }} {{ $intern->student->profile->last_name }}</p>
                                    <p><strong>Course:</strong> {{ $intern->student->course->course_code }}</p>
                                    <p><strong>Email:</strong> {{ $intern->student->email }}</p>
                                    <p><strong>Job Title:</strong> {{ $intern->job->title }}</p>
                                    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($intern->start_date)->format('F d, Y') }}</p>
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
