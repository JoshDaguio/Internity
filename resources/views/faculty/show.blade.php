@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Faculty Account Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="{{ route('faculty.index') }}">Faculty</a></li>
            <li class="breadcrumb-item active">{{$faculty->profile->first_name . ' ' . $faculty->profile->last_name}}</li>
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

<div class="card mb-4">

    <div class="card-body">
        <h5 class="card-title">{{ $faculty->profile ? $faculty->profile->first_name . ' ' . $faculty->profile->last_name : 'N/A' }}</h5>

        <!-- Faculty Details Section -->
        <div class="row align-items-center mb-4">
            <!-- Profile Picture -->
            <div class="col-sm-12 col-md-3 d-flex justify-content-center mb-3 mb-md-0">
                <img id="profilePicturePreview" src="{{ $faculty->profile->profile_picture ? asset('storage/' . $faculty->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" width="150">
            </div>

            <!-- Faculty Info -->
            <div class="col-sm-12 col-md-9">
                <p><strong><i class="bi bi-card-text me-2"></i> ID Number:</strong> {{ $faculty->profile->id_number ?? 'N/A' }}</p>
                <p><strong><i class="bi bi-envelope me-2"></i> Email:</strong> {{ $faculty->email }}</p>
                <p><strong><i class="bi bi-book me-2"></i> Course:</strong> {{ $faculty->course->course_code ?? 'N/A' }}</p>
                <p><strong><i class="bi bi-person-check-fill me-2"></i> Status:</strong> 
                    <span class="badge {{ $faculty->status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                        {{ $faculty->status_id == 1 ? 'Active' : 'Inactive' }}
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
            <div class="col-md-9">{{ $faculty->profile->about ?? 'N/A' }}</div>
        </div>

        <hr class="my-4">

        <!-- Links Section -->
        <div class="row">
            <div class="col-md-3">
                <strong><i class="bi bi-link-45deg me-2"></i> Links:</strong>
            </div>
            <div class="col-md-9">
                @if($faculty->profile->links->isEmpty())
                    <p>No links available.</p>
                @else
                    <ul class="list-unstyled">
                        @foreach($faculty->profile->links as $link)
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
                <a href="{{ route('faculty.edit', $faculty) }}" class="btn btn-warning me-2 {{ $faculty->status_id != 1 ? 'd-none' : '' }}">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>

                @if($faculty->status_id == 1)
                    <!-- Deactivate button -->
                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $faculty->id }}">
                        <i class="bi bi-trash me-1"></i> Deactivate
                    </button>

                    <!-- Deactivate Modal -->
                    <div class="modal fade" id="deactivateModal-{{ $faculty->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deactivateModalLabel">Deactivate Faculty Account</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to deactivate this faculty account?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('faculty.destroy', $faculty) }}" method="POST" style="display:inline;">
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

                @if($faculty->status_id == 2)
                    <!-- Reactivate button -->
                    <form action="{{ route('faculty.reactivate', $faculty) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-arrow-repeat me-1"></i> Reactivate
                        </button>
                    </form>
                @endif

                @if(Auth::user()->role_id == 1)
                <!-- Promote button -->
                    <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#promoteModal-{{ $faculty->id }}">
                        <i class="bi bi-arrow-up-circle me-1"></i> Promote Account
                    </button>

                    <!-- Promote Modal -->
                    <div class="modal fade" id="promoteModal-{{ $faculty->id }}" tabindex="-1" aria-labelledby="promoteModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="promoteModalLabel">Promote Faculty to Admin</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin-accounts.promote', $faculty) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        <p>Enter your password to confirm promotion.</p>
                                        <div class="col-12 mt-4">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group has-validation">
                                                <input type="password" name="password" class="form-control" id="password" required placeholder="Enter your password">
                                                <div class="invalid-feedback">Please enter your password.</div>
                                            </div>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Confirm Promotion</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
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
                                @if($log->action == 'Updated File')
                                    @php
                                        $changes = json_decode($log->changes, true);
                                    @endphp

                                    @if (!empty($changes))
                                        <ul>
                                            @foreach ($changes as $field => $change)
                                                @if(is_array($change))
                                                    <li>{{ ucfirst($field) }}: from <strong>{{ $change['old'] }}</strong> to <strong>{{ $change['new'] }}</strong></li>
                                                @else
                                                    <li>{{ ucfirst($field) }}: {{ $change }}</li>
                                                @endif
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
