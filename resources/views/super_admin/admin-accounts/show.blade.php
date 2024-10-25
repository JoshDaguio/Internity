@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Admin Account Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="{{ route('admin-accounts.index') }}">Admin</a></li>
            <li class="breadcrumb-item active">{{ $admin->profile->first_name . ' ' . $admin->profile->last_name}}</li>
        </ol>
    </nav>
</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<!-- Back to List Button -->
<a href="{{ route('admin-accounts.index') }}" class="btn btn-secondary mb-3">Back</a>


<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">{{ $admin->profile ? $admin->profile->first_name . ' ' . $admin->profile->last_name : 'N/A' }}</h5>

        <!-- Admin Details Section -->
        <div class="row align-items-center mb-4">
            <!-- Profile Picture -->
            <div class="col-sm-12 col-md-3 d-flex justify-content-center mb-3 mb-md-0">
                <img id="profilePicturePreview" src="{{ $admin->profile->profile_picture ? asset('storage/' . $admin->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" width="150">
            </div>

            <!-- Admin Info -->
            <div class="col-sm-12 col-md-9">
                <p><strong><i class="bi bi-card-text me-2"></i> ID Number:</strong> {{ $admin->profile->id_number ?? 'N/A' }}</p>
                <p><strong><i class="bi bi-envelope me-2"></i> Email:</strong> {{ $admin->email }}</p>
                <p><strong><i class="bi bi-person-check-fill me-2"></i> Status:</strong>
                    <span class="badge {{ $admin->status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                        {{ $admin->status_id == 1 ? 'Active' : 'Inactive' }}
                    </span>
                </p>
            </div>
        </div>

        <hr class="my-4">

        <!-- About Section -->
        <div class="row">
            <div class="col-md-3">
                <strong><i class="bi bi-info-circle me-2"></i> About:</strong>
            </div>
            <div class="col-md-9">{{ $admin->profile->about ?? 'N/A' }}</div>
        </div>

        <hr class="my-4">

        <!-- Links Section -->
        <div class="row">
            <div class="col-md-3">
                <strong><i class="bi bi-link-45deg me-2"></i> Links:</strong>
            </div>
            <div class="col-md-9">
                @if($admin->profile->links->isEmpty())
                    <p>No links available.</p>
                @else
                    <ul class="list-unstyled">
                        @foreach($admin->profile->links as $link)
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
                <a href="{{ route('admin-accounts.edit', $admin) }}" class="btn btn-warning me-2">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>

                @if($admin->status_id == 1)
                    <!-- Deactivate button -->
                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $admin->id }}">
                        <i class="bi bi-trash me-1"></i> Deactivate
                    </button>

                    <!-- Deactivate Modal -->
                    <div class="modal fade" id="deactivateModal-{{ $admin->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deactivateModalLabel">Deactivate Admin Account</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to deactivate this admin account?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('admin-accounts.destroy', $admin) }}" method="POST" style="display:inline;">
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

                @if($admin->status_id == 2)
                    <!-- Reactivate button -->
                    <form action="{{ route('admin-accounts.reactivate', $admin) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-arrow-repeat me-1"></i> Reactivate
                        </button>
                    </form>
                @endif

                <!-- Demote button -->
                <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#demoteModal-{{ $admin->id }}">
                    <i class="bi bi-arrow-down-circle me-1"></i> Demote Account
                </button>

                <!-- Demote Modal -->
                <div class="modal fade" id="demoteModal-{{ $admin->id }}" tabindex="-1" aria-labelledby="demoteModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="demoteModalLabel">Demote Admin to Faculty</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin-accounts.demote', $admin) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="modal-body">
                                    <p>Enter your password and select a course for this account.</p>
                                    <div class="col-12 mt-4">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group has-validation">
                                            <input type="password" name="password" class="form-control" id="password" required placeholder="Enter your password">
                                            <div class="invalid-feedback">Please enter your password.</div>
                                        </div>
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>

                                    <div class="mb-3">
                                        <label for="course_id" class="form-label">Assign Course</label>
                                        <select name="course_id" class="form-select" required>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->course_code }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Confirm Demotion</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<hr> 

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Activity Logs</h5>
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Changes</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ ucfirst($log->action) }}</td>
                            <td>{{ ucfirst($log->target) }}</td>
                            <td>
                                @if($log->changes)
                                    @php
                                        $changes = json_decode($log->changes, true);
                                    @endphp
                                    @if (!empty($changes))
                                        <ul>
                                            @foreach ($changes as $field => $change)
                                                <li>{{ ucfirst($field) }}: from <strong>{{ $change['old'] }}</strong> to <strong>{{ $change['new'] }}</strong></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        No specific changes recorded.
                                    @endif
                                @else
                                    No changes available
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('F d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
