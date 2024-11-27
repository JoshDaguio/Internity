@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Company Account Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Company</li>
                <li class="breadcrumb-item active">{{ $company->name }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <a href="{{ route('company.index') }}" class="btn btn-secondary mb-3 btn-sm">Back</a>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ $company->name }}</h5>

            <!-- Company Details Section -->
            <div class="row align-items-center mb-4">
                <!-- Profile Picture -->
                <div class="col-sm-12 col-md-3 d-flex justify-content-center mb-3 mb-md-0">
                    <img id="profilePicturePreview" src="{{ $company->profile->profile_picture ? asset('storage/' . $company->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" width="150">
                </div>

                <!-- Company Info -->
                <div class="col-sm-12 col-md-9">
                    <p><strong><i class="bi bi-envelope me-2"></i> Email:</strong> {{ $company->email }}</p>
                    <p><strong><i class="bi bi-person-circle me-2"></i> Contact Person:</strong> {{ $company->profile->first_name }} {{ $company->profile->last_name }}</p>
                    <p><strong><i class="bi bi-telephone me-2"></i> Contact Number:</strong> {{ $company->profile->contact_number ?? 'N/A' }}</p>
                    <p><strong><i class="bi bi-geo-alt me-2"></i> Company Address:</strong> {{ $company->profile->address ?? 'N/A' }}</p>
                    <p><strong><i class="bi bi-person-check-fill me-2"></i> Status:</strong>
                        <span class="badge {{ $company->status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                            {{ $company->status_id == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                </div>
            </div>


            <hr class="my-4">

            <!-- MOA Section -->
            <div class="row">
                <div class="col-md-3">
                    <strong><i class="bi bi-file-earmark-text me-2"></i> MOA File:</strong>
                </div>
                <div class="col-md-9">
                    @if($company->profile->moa_file_path)
                        <!-- Button to trigger modal -->
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#moaModal">
                            <i class="bi bi-folder"></i>
                        </button>

                        <!-- MOA Modal -->
                        <div class="modal fade" id="moaModal" tabindex="-1" aria-labelledby="moaModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="moaModalLabel">Memorandum of Agreement (MOA)</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe 
                                            src="{{ asset('storage/' . $company->profile->moa_file_path) }}" 
                                            style="width: 100%; height: 500px;" 
                                            frameborder="0">
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <span class="badge bg-warning">No MOA Uploaded</span>
                    @endif
                </div>
            </div>

            <hr class="my-4">

            <!-- About Section -->
            <div class="row">
                <div class="col-md-3">
                    <strong><i class="bi bi-info-circle me-2"></i> About:</strong>
                </div>
                <div class="col-md-9">{{ $company->profile->about ?? 'N/A' }}</div>
            </div>

            <hr class="my-4">

            <!-- Links Section -->
            <div class="row">
                <div class="col-md-3">
                    <strong><i class="bi bi-link-45deg me-2"></i> Links:</strong>
                </div>
                <div class="col-md-9">
                    @if($company->profile->links->isEmpty())
                        <p>No links available.</p>
                    @else
                        <ul class="list-unstyled">
                            @foreach($company->profile->links as $link)
                                <li><a href="{{ $link->link_url }}" target="_blank">{{ $link->link_name }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <hr class="my-4">

            <!-- Action Buttons Section -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <a href="{{ route('company.edit', $company->id) }}" class="btn btn-warning me-2 btn-sm {{ $company->status_id != 1 ? 'd-none' : '' }}">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>

                    @if($company->status_id == 1)
                        <!-- Deactivate button -->
                        <button type="button" class="btn btn-danger me-2 btn-sm" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                            <i class="bi bi-trash me-1"></i> Deactivate
                        </button>

                        <!-- Deactivate Modal -->
                        <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deactivateModalLabel">Deactivate Company Account</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to deactivate this company account?
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('company.destroy', $company->id) }}" method="POST" style="display:inline;">
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

                    @if($company->status_id != 1)
                        <!-- Reactivate button -->
                        <form action="{{ route('company.reactivate', $company->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-arrow-repeat me-1"></i> Reactivate
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <hr> 

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Jobs Posted</h5>
            <div class="table-responsive">

                @if($company->jobs->isEmpty())
                    <p>No jobs posted by this company.</p>
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Available</th>
                                <th>Work Type</th>
                                <th>Schedule</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->jobs as $job)
                                <tr>
                                    <td>
                                        <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-light btn-sm">
                                            {{ $job->title }}
                                        </a>
                                    </td>
                                    <td>{{ $job->positions_available }}</td>
                                    <td>{{ $job->work_type }}</td>
                                    <td>
                                        @php
                                            $schedule = json_decode($job->schedule, true);
                                        @endphp
                                        Days: {{ implode(', ', $schedule['days'] ?? []) }} <br>
                                        @if ($job->work_type === 'Hybrid')
                                            On-site Days: {{ implode(', ', $schedule['onsite_days'] ?? []) }} <br><br>
                                            Remote Days: {{ implode(', ', $schedule['remote_days'] ?? []) }} <br>
                                        @endif
                                        Time: {{ \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('h:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
