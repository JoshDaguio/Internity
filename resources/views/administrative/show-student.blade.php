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

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">{{ $student->profile ? $student->profile->first_name . ' ' . $student->profile->last_name : 'N/A' }}</h5>

        <div class="row align-items-center">
            <!-- Profile Picture Section -->
            <div class="col-sm-12 col-md-3 d-flex justify-content-center mb-3 mb-md-0">
                <img id="profilePicturePreview" src="{{ $student->profile->profile_picture ? asset('storage/' . $student->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" width="150">
            </div>
            
            <!-- Student Details Section -->
            <div class="col-sm-12 col-md-9">
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
                    <button class="btn btn-dark" onclick="showPreview('{{ route('profile.previewCV', $student->profile->id) }}')">
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

        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2) <!-- Only admins/super admins can see this -->
        <hr class="my-4">   
            <div class="row">
                <div class="col-md-3">
                    <strong><i class="bi bi-exclamation-circle-fill me-2"></i> Irregular Student:</strong>
                </div>
                <div class="col-md-9">
                    <form action="{{ route('students.markIrregular', $student->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="isIrregular" name="is_irregular" {{ $student->profile->is_irregular ? 'checked' : '' }}>
                            <label class="form-check-label" for="isIrregular">Mark as Irregular</label>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Update</button>
                    </form>
                </div>
            </div>
            <hr class="my-4">
        @endif

@if( Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
    @if($student->acceptedInternship && ($student->profile->is_irregular))
        <h5 class="card-title">Custom Schedule for Irregular Student</h5>
        <form action="{{ route('students.updateSchedule', $student->id) }}" method="POST">
            @csrf
            @method('PATCH')

            @php
                // Ensure the schedule is an array. If it's not, decode it.
                $savedSchedule = is_array($student->acceptedInternship->schedule) 
                    ? $student->acceptedInternship->schedule 
                    : json_decode($student->acceptedInternship->schedule, true);

                // Assign days from the schedule
                $daysOfWeek = $savedSchedule['days']; // Regular days

                // Get on-site and remote days if they exist for hybrid schedule
                $onsiteDays = $savedSchedule['onsite_days'] ?? []; // On-site days
                $remoteDays = $savedSchedule['remote_days'] ?? []; // Remote days

                $startTime = $savedSchedule['start_time'];
                $endTime = $savedSchedule['end_time'];

                // Decode custom schedule if available
                $customSchedule = isset($student->acceptedInternship->custom_schedule) && is_array($student->acceptedInternship->custom_schedule)
                    ? $student->acceptedInternship->custom_schedule
                    : json_decode($student->acceptedInternship->custom_schedule ?? '[]', true);
            @endphp


            <!-- Regular Days -->
            @foreach($daysOfWeek as $day)
                <div class="row mb-2">
                    <div class="col-md-3">
                        <label>{{ ucfirst($day) }}</label>
                    </div>
                    <div class="col-md-4">
                        <input type="time" name="schedule[{{ $day }}][start]" class="form-control" value="{{ $customSchedule[$day]['start'] ?? $startTime }}">
                    </div>
                    <div class="col-md-4">
                        <input type="time" name="schedule[{{ $day }}][end]" class="form-control" value="{{ $customSchedule[$day]['end'] ?? $endTime }}">
                    </div>
                </div>
            @endforeach

            <!-- On-site Days for Hybrid -->
            @if($student->acceptedInternship->work_type === 'Hybrid' && !empty($onsiteDays))
                <h6><strong>On-site Days:</strong></h6>
                @foreach($onsiteDays as $onsiteDay)
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label>{{ ucfirst($onsiteDay) }}</label>
                        </div>
                        <div class="col-md-4">
                            <input type="time" name="schedule[{{ $onsiteDay }}][start]" class="form-control" value="{{ $customSchedule[$onsiteDay]['start'] ?? $startTime }}">
                        </div>
                        <div class="col-md-4">
                            <input type="time" name="schedule[{{ $onsiteDay }}][end]" class="form-control" value="{{ $customSchedule[$onsiteDay]['end'] ?? $endTime }}">
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Remote Days for Hybrid -->
            @if($student->acceptedInternship->work_type === 'Hybrid' && !empty($remoteDays))
                <h6><strong>Remote Days:</strong></h6>
                @foreach($remoteDays as $remoteDay)
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label>{{ ucfirst($remoteDay) }}</label>
                        </div>
                        <div class="col-md-4">
                            <input type="time" name="schedule[{{ $remoteDay }}][start]" class="form-control" value="{{ $customSchedule[$remoteDay]['start'] ?? $startTime }}">
                        </div>
                        <div class="col-md-4">
                            <input type="time" name="schedule[{{ $remoteDay }}][end]" class="form-control" value="{{ $customSchedule[$remoteDay]['end'] ?? $endTime }}">
                        </div>
                    </div>
                @endforeach
            @endif

            <button type="submit" class="btn btn-primary mt-3">Save Schedule</button>
        </form>
        <hr class="my-4">
    @endif

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
                                    <h5 class="modal-title" id="deactivateModalLabel">Deactivate Student Account</h5>
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
        @endif
    </div>
</div>


<!-- Internship Details and Priority Card -->
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
                    <strong><i class="bi bi-person-badge me-2"></i> Job Title:</strong>
                </div>
                <div class="col-md-9">
                    {{ $student->acceptedInternship->job->title }}
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <strong><i class="bi bi-geo-alt-fill me-2"></i> Work Type:</strong>
                </div>
                <div class="col-md-9">
                    {{ ucfirst($student->acceptedInternship->work_type) }}
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-3">
                    <strong><i class="bi bi-calendar-week me-2"></i> Schedule:</strong>
                </div>
                <div class="col-md-9">
                    @php
                        // Helper function to format time to 12-hour format
                        function formatTimeTo12Hour($time) {
                            return \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                        }

                        $savedSchedule = is_array($student->acceptedInternship->schedule) 
                            ? $student->acceptedInternship->schedule 
                            : json_decode($student->acceptedInternship->schedule, true);

                        $customSchedule = isset($student->acceptedInternship->custom_schedule) && is_array($student->acceptedInternship->custom_schedule)
                            ? $student->acceptedInternship->custom_schedule
                            : json_decode($student->acceptedInternship->custom_schedule ?? '[]', true);
                    @endphp

                    <!-- Loop through regular days -->
                    @foreach($savedSchedule['days'] as $day)
                        <p><strong>{{ ucfirst($day) }}:</strong>
                            @if(isset($customSchedule[$day]))
                                {{ formatTimeTo12Hour($customSchedule[$day]['start']) }} - {{ formatTimeTo12Hour($customSchedule[$day]['end']) }} 
                            @else
                                {{ formatTimeTo12Hour($savedSchedule['start_time']) }} - {{ formatTimeTo12Hour($savedSchedule['end_time']) }} 
                            @endif
                        </p>
                    @endforeach

                    <!-- Handle Hybrid schedules -->
                    @if($student->acceptedInternship->work_type === 'Hybrid')
                        <!-- On-site days -->
                        @if(!empty($savedSchedule['onsite_days']))
                            <p><strong>On-site Days:</strong></p>
                            @foreach($savedSchedule['onsite_days'] as $onsiteDay)
                                <p>{{ ucfirst($onsiteDay) }}: 
                                    @if(isset($customSchedule[$onsiteDay]))
                                        {{ formatTimeTo12Hour($customSchedule[$onsiteDay]['start']) }} - {{ formatTimeTo12Hour($customSchedule[$onsiteDay]['end']) }} 
                                    @else
                                        {{ formatTimeTo12Hour($savedSchedule['start_time']) }} - {{ formatTimeTo12Hour($savedSchedule['end_time']) }} 
                                    @endif
                                </p>
                            @endforeach
                        @endif

                        <!-- Remote days -->
                        @if(!empty($savedSchedule['remote_days']))
                            <p><strong>Remote Days:</strong></p>
                            @foreach($savedSchedule['remote_days'] as $remoteDay)
                                <p>{{ ucfirst($remoteDay) }}: 
                                    @if(isset($customSchedule[$remoteDay]))
                                        {{ formatTimeTo12Hour($customSchedule[$remoteDay]['start']) }} - {{ formatTimeTo12Hour($customSchedule[$remoteDay]['end']) }} 
                                    @else
                                        {{ formatTimeTo12Hour($savedSchedule['start_time']) }} - {{ formatTimeTo12Hour($savedSchedule['end_time']) }} 
                                    @endif
                                </p>
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <a href="{{ route('students.edit', $student) }}" class="btn btn-primary me-2 {{ $student->status_id != 1 ? 'd-none' : '' }}">
                        <i class="bi bi bi-journal-text"></i> End of Day Reports
                    </a>

                    <a href="{{ route('students.dtr', $student->id) }}" class="btn btn-success me-2 {{ $student->status_id != 1 ? 'd-none' : '' }}">
                        <i class="bi bi bi-clock"></i> Daily Time Record
                    </a>
                </div>
            </div>            
        @else
            <p class="fst-italic">No Internship Yet.</p>
        @endif

        @if( Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3)

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
