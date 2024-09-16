@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Job Applications for {{ $job->title }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Interns</li>
                <li class="breadcrumb-item">Applications</li>
                <li class="breadcrumb-item active">{{ $job->title }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <!-- Back Button -->
    <a href="{{ route('company.internApplications') }}" class="btn btn-secondary mb-3">Back to Intern Applications</a>
    
    <!-- Job Information Section -->
    <div class="row mb-0">
        <!-- Job Information Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Job Information</h5>
                    <p><strong>Available Positions:</strong> {{ $job->positions_available }}</p>
                    
                    <!-- Schedule -->
                    @php
                        $schedule = json_decode($job->schedule, true);
                        $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                        $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                    @endphp
                    
                    <p><strong>Work Type & Schedule:</strong></p>
                    <ul class="list-unstyled">
                        <li><strong>Work Type:</strong> {{ $job->work_type }}</li>
                        <p><strong>Schedule:</strong></p>
                        <li><strong>Days:</strong> {{ implode(', ', $schedule['days'] ?? []) }}</li>
                        @if ($job->work_type === 'Hybrid')
                            <li><strong>On-site Days:</strong> {{ implode(', ', $schedule['onsite_days'] ?? []) }}</li>
                            <li><strong>Remote Days:</strong> {{ implode(', ', $schedule['remote_days'] ?? []) }}</li>
                        @endif
                        <li><strong>Time:</strong> {{ $startTime }} - {{ $endTime }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Job Details Accordion Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Job Details</h5>

                    <!-- Accordion for Description, Qualification, Preferred Skills -->
                    <div class="accordion" id="jobDetailsAccordion">
                        <!-- Description Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDescription">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescription" aria-expanded="false" aria-controls="collapseDescription">
                                    Description
                                </button>
                            </h2>
                            <div id="collapseDescription" class="accordion-collapse collapse" aria-labelledby="headingDescription" data-bs-parent="#jobDetailsAccordion">
                                <div class="accordion-body">
                                    {{ $job->description }}
                                </div>
                            </div>
                        </div>

                        <!-- Qualification Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingQualification">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseQualification" aria-expanded="false" aria-controls="collapseQualification">
                                    Qualification
                                </button>
                            </h2>
                            <div id="collapseQualification" class="accordion-collapse collapse" aria-labelledby="headingQualification" data-bs-parent="#jobDetailsAccordion">
                                <div class="accordion-body">
                                    {{ $job->qualification }}
                                </div>
                            </div>
                        </div>

                        <!-- Preferred Skills Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSkills">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSkills" aria-expanded="false" aria-controls="collapseSkills">
                                    Preferred Skills
                                </button>
                            </h2>
                            <div id="collapseSkills" class="accordion-collapse collapse" aria-labelledby="headingSkills" data-bs-parent="#jobDetailsAccordion">
                                <div class="accordion-body">
                                    {{ $job->skillTags->pluck('name')->implode(', ') }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Applicants Section -->

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Recommended Applicants</h5>
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Matching Skills</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recommendedApplicants as $application)
                        @php
                            $matchingSkills = $application->student->profile->skillTags->pluck('name')->implode(', ');
                        @endphp
                        <tr>
                            <td>{{ $application->student->profile->first_name }} {{ $application->student->profile->last_name }}</td>
                            <td>{{ $matchingSkills }}</td>
                            <td>{{ $application->status->status }}</td>
                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewApplicantModal{{ $application->id }}"><i class="bi bi-info-circle"></i></button>
                            </td>
                        </tr>

                        <!-- Applicant Modal -->
                        <div class="modal fade" id="viewApplicantModal{{ $application->id }}" tabindex="-1" aria-labelledby="viewApplicantModalLabel{{ $application->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewApplicantModalLabel{{ $application->id }}">Applicant Information</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Full Name:</strong> {{ $application->student->profile->first_name }} {{ $application->student->profile->last_name }}</p>
                                        <p><strong>Course:</strong> {{ $application->student->course->course_code }}</p>
                                        <p><strong>Email:</strong> {{ $application->student->email }}</p>
                                        <p><strong>About:</strong> {{ $application->student->profile->about ?? 'N/A' }}</p>
                                        <p><strong>Skills:</strong> {{ $application->student->profile->skillTags->pluck('name')->implode(', ') }}</p>
                                        <p><strong>Links:</strong></p>
                                        <ul>
                                            @foreach ($application->student->profile->links as $link)
                                            <li><a href="{{ $link->link_url }}" target="_blank">{{ $link->link_name }}</a></li>
                                            @endforeach
                                        </ul>
                                        <p><strong>Endorsement Letter:</strong> 
                                            <button class="btn btn-dark" onclick="showPreview('{{ route('application.preview', ['type' => 'endorsement_letter', 'id' => $application->id]) }}')"><i class="bi bi-folder"></i></button>
                                        </p>
                                        <p><strong>CV:</strong> 
                                            <button class="btn btn-dark" onclick="showPreview('{{ route('application.preview', ['type' => 'cv', 'id' => $application->id]) }}')"><i class="bi bi-folder"></i></button>
                                        </p>
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

    <!-- Other Applicants Section -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Other Applicants</h5>
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($otherApplicants as $application)
                        <tr>
                            <td>{{ $application->student->profile->first_name }} {{ $application->student->profile->last_name }}</td>
                            <td>{{ $application->status->status }}</td>
                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewApplicantModal{{ $application->id }}"><i class="bi bi-info-circle"></i></button>
                            </td>
                        </tr>
                                    <!-- Applicant Modal -->
                                    <div class="modal fade" id="viewApplicantModal{{ $application->id }}" tabindex="-1" aria-labelledby="viewApplicantModalLabel{{ $application->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewApplicantModalLabel{{ $application->id }}">Applicant Information</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Full Name:</strong> {{ $application->student->profile->first_name }} {{ $application->student->profile->last_name }}</p>
                                        <p><strong>Course:</strong> {{ $application->student->course->course_code }}</p>
                                        <p><strong>Email:</strong> {{ $application->student->email }}</p>
                                        <p><strong>About:</strong> {{ $application->student->profile->about ?? 'N/A' }}</p>
                                        <p><strong>Skills:</strong> {{ $application->student->profile->skillTags->pluck('name')->implode(', ') }}</p>
                                        <p><strong>Links:</strong></p>
                                        <ul>
                                            @foreach ($application->student->profile->links as $link)
                                            <li><a href="{{ $link->link_url }}" target="_blank">{{ $link->link_name }}</a></li>
                                            @endforeach
                                        </ul>
                                        <p><strong>Endorsement Letter:</strong> 
                                            <button class="btn btn-secondary" onclick="showPreview('{{ route('application.preview', ['type' => 'endorsement_letter', 'id' => $application->id]) }}')"><i class="bi bi-folder"></i></button>
                                        </p>
                                        <p><strong>CV:</strong> 
                                            <button class="btn btn-secondary" onclick="showPreview('{{ route('application.preview', ['type' => 'cv', 'id' => $application->id]) }}')"><i class="bi bi-folder"></i></button>
                                        </p>
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
