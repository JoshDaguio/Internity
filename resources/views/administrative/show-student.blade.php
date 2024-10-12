@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Student Account Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="javascript:history.back()">Students</a></li>
            <li class="breadcrumb-item active">{{ $student->profile->first_name }} {{ $student->profile->last_name }}</li>
        </ol>
    </nav>
</div>

<a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

<div class="card mb-3">
    <div class="card-body">
    <h5 class="card-title">{{ $student->profile ? $student->profile->first_name . ' ' . $student->profile->last_name : 'N/A' }}</h5>

        <div class="card-body d-flex align-items-center">
            <img id="profilePicturePreview" src="{{ $student->profile->profile_picture ? asset('storage/' . $student->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" width="150">
            <div class="ms-4">
                <p><strong><i class="bi bi-card-text me-2"></i> ID Number:</strong> {{ $student->profile->id_number ?? 'N/A' }}</p>
                <p><strong><i class="bi bi-envelope me-2"></i> Email:</strong> {{ $student->email }}</p>
                <p><strong><i class="bi bi-book me-2"></i> Course:</strong> {{ $student->course->course_code ?? 'N/A' }}</p>
                <p><strong><i class="bi bi-person-check-fill me-2"></i> Status:</strong> 
                    <span class="badge {{ $student->status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                        {{ $student->status_id == 1 ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
        </div>

        <hr class="my-4">

        <!-- CV Section -->
        <div class="row">
            <div class="col-md-3">
                <strong><i class="bi bi-file-earmark-person-fill me-2"></i> Curriculum Vitae:</strong>
            </div>
            <div class="col-md-9">
                @if ($student->profile->cv_file_path)
                    <button class="btn btn-primary" onclick="showPreview('{{ route('profile.previewCV', $student->profile->id) }}')">
                        <i class="bi bi-folder"></i>
                    </button>
                @else
                    <p class="fst-italic">No CV uploaded.</p>
                @endif
            </div>
        </div>

        <hr class="my-4">


        <!-- Skills Section -->
        <div class="row">
            <div class="col-md-3">
                <strong><i class="bi bi-lightbulb-fill me-2"></i> Skills:</strong>
            </div>
            <div class="col-md-9">
                @if($student->profile->skillTags->isEmpty())
                    <p class="fst-italic">No skills available.</p>
                @else
                    {{ $student->profile->skillTags->pluck('name')->implode(', ') }}
                @endif
            </div>
        </div>

        <hr class="my-4">

        <!-- About Section -->
        <div class="row">
            <div class="col-md-3">
                <strong><i class="bi bi-info-circle me-2"></i> About:</strong>
            </div>
            <div class="col-md-9">{{ $student->profile->about ?? 'N/A' }}</div>
        </div>

        <hr class="my-4">

        <!-- Links Section -->
        <div class="row">
            <div class="col-md-3">
                <strong><i class="bi bi-link-45deg me-2"></i> Links:</strong>
            </div>
            <div class="col-md-9">
                @if($student->profile->links->isEmpty())
                    <p class="fst-italic">No links available.</p>
                @else
                    <ul class="list-unstyled">
                        @foreach($student->profile->links as $link)
                            <li><a href="{{ $link->link_url }}" target="_blank">{{ $link->link_name }}</a></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <hr class="my-4">

        <!-- Action Buttons Section -->
        <div class="row mt-4 text">
            <div class="col-md-12">
                <a href="{{ route('students.edit', $student) }}" class="btn btn-warning me-2 {{ $student->status_id != 1 ? 'd-none' : '' }}">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>

                @if($student->status_id == 1)
                    <!-- Deactivate button -->
                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $student->id }}">
                        <i class="bi bi-trash me-1"></i> Deactivate
                    </button>

                    <!-- Deactivate Modal -->
                    <div class="modal fade" id="deactivateModal-{{ $student->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deactivateModalLabel">Deactivate Faculty Account</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to deactivate this student account?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('students.deactivate', $student) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Deactivate</button>
                                    </form>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($student->status_id == 2)
                    <!-- Reactivate button -->
                    <form action="{{ route('students.reactivate', $student) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-arrow-repeat me-1"></i> Reactivate
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Priority Companies Card -->
<div class="card">
    <div class="card-body">
        <!-- Internship Section -->
        <h5 class="card-title">Internship Details</h5>
        @if ($student->acceptedInternship)
            <div class="row">
                <div class="col-md-3">
                    <strong><i class="bi bi-building me-2"></i> Company Name:</strong>
                </div>
                <div class="col-md-9">
                    {{ $student->acceptedInternship->job->company->name }}
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <strong><i class="bi bi-person-badge me-2"></i> Position:</strong>
                </div>
                <div class="col-md-9">
                    {{ $student->acceptedInternship->job->title }}
                </div>
            </div>
        @else
            <p class="fst-italic">No Internship Yet.</p>
        @endif

        <hr class="my-4">

        <h5 class="card-title">Priority Companies</h5>
        @if($priorityListings->isEmpty())
            <p class="fst-italic">No priority companies selected.</p>
        @else
            <ul class="list-unstyled">
                @foreach($priorityListings as $priority)
                    <li>{{ $priority->job->company->name }} - {{ $priority->job->title }} (Priority: {{ $priority->priority }})</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
                    <!-- Modal for CV preview -->
                    <div class="modal fade" id="cvPreviewModal" tabindex="-1" aria-labelledby="cvPreviewLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cvPreviewLabel">CV Preview</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe id="cvPreviewIframe" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        function showPreview(url) {
                            document.getElementById('cvPreviewIframe').src = url;
                            var cvPreviewModal = new bootstrap.Modal(document.getElementById('cvPreviewModal'), {});
                            cvPreviewModal.show();
                        }
                    </script>
@endsection
