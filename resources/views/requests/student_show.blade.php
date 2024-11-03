@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Excusal Requests Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Requests</li>
                <li class="breadcrumb-item active">{{ $request->subject }}</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('requests.studentIndex') }}" class="btn btn-secondary btn-sm mb-3">Back</a>

    <div class="row mb-3">
        <!-- <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Internship Details</h5>

                    <p><strong><i class="bi bi-building me-2"></i> Company:</strong> {{ $student->acceptedInternship->job->company->name}}</p>
                    <p><strong><i class="bi bi-person-badge me-2"></i> Job:</strong> {{ $student->acceptedInternship->job->title }}</p>
                    <p><strong><i class="bi bi-geo-alt-fill me-2"></i> Work Type:</strong> {{ ucfirst($student->acceptedInternship->work_type) }}</p>
                    <p><strong><i class="bi bi-calendar-week me-2"></i> Schedule:</strong></p>
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

                    @foreach($savedSchedule['days'] as $day)
                        <p><strong>{{ ucfirst($day) }}:</strong>
                            @if(isset($customSchedule[$day]))
                                {{ formatTimeTo12Hour($customSchedule[$day]['start']) }} - {{ formatTimeTo12Hour($customSchedule[$day]['end']) }} 
                            @else
                                {{ formatTimeTo12Hour($savedSchedule['start_time']) }} - {{ formatTimeTo12Hour($savedSchedule['end_time']) }} 
                            @endif
                        </p>
                    @endforeach

                    @if($student->acceptedInternship->work_type === 'Hybrid')
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
        </div> -->
        <div class="col-md-7">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Requests Details</h5>
<!-- 
                    <p><strong>Company:</strong> {{ $student->acceptedInternship->job->company->name}}</p>
                    <p><strong>Job Title:</strong> {{ $student->acceptedInternship->job->title }}</p> -->

                    <p><strong>Subject:</strong> {{ $request->subject }}</p>
                    <p><strong>Reason:</strong> {{ $request->reason }}</p>
                    <p><strong>Date of Absence:</strong> {{ \Carbon\Carbon::parse($request->absence_date)->format('F d, Y') }}</p>
                    <p><strong>Attached File:</strong>
                        @if ($request->attachment_path)
                            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#attachmentModal">
                                <i class="bi bi-folder"></i>
                            </button>
                        @else
                            <p><code>No file attached</code></p>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Admin Response</h5>
                    <p><strong>Status:</strong> <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($request->status) }}</span></p>
                    <p><strong>Penalty:</strong> {{ $request->penalty_type ? ucfirst(str_replace('_', ' ', $request->penalty_type)) : 'N/A' }}</p>
                    <p><strong>Remarks:</strong> {{ $request->admin_remarks ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>


<!-- Modal for Attachment Preview -->
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

<script>
    function openFilePreview(url) {
        window.open(url, '_blank');
    }
</script>
@endsection