@extends('layouts.app')
@section('body')

<div class="pagetitle">
    <h1>Profile</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

   <!-- Success and Error Messages -->
   @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<section class="section profile">
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    <img id="profilePicturePreview" src="{{ $profile->profile_picture ? asset('storage/' . $profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle">
                    <h2>{{ $profile->first_name }} {{ $profile->middle_name ? $profile->middle_name[0].'.' : '' }} {{ $profile->last_name }}</h2>
                    <h3>{{ $user->role->role_name }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body pt-3">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered">
                        <li class="nav-item">
                            <button class="nav-link {{ session('status') === 'password-updated' || $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation') ? '' : 'active' }}" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link {{ session('status') === 'password-updated' || $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation') ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                        </li>
                    </ul>
                    <div class="tab-content pt-2">
                        <div class="tab-pane fade {{ session('status') === 'password-updated' || $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation') ? '' : 'show active' }} profile-overview" id="profile-overview">
                            <h5 class="card-title">About</h5>
                            <p class="small fst-italic">{{ $profile->about ?? 'N/A' }}</p>

                            <!-- Profile Details -->
                            @if($user->role_id == 4) <!-- Company Profile -->
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Company Name</div>
                                    <div class="col-lg-9 col-md-8">{{ $user->name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Contact Person</div>
                                    <div class="col-lg-9 col-md-8">{{ $profile->first_name }} {{ $profile->last_name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Contact Number</div>
                                    <div class="col-lg-9 col-md-8">{{ $profile->contact_number ?? 'N/A' }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Address</div>
                                    <div class="col-lg-9 col-md-8">{{ $profile->address ?? 'N/A' }}</div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Memorandum of Agreement</div>
                                    <div class="col-lg-9 col-md-8">
                                        @if ($profile->moa_file_path)
                                            <button class="btn btn-primary" onclick="showPreview('{{ route('profile.previewMOA', $profile->id) }}')"><i class="bi bi-folder"></i></button>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>

                            @else
                                <!-- Common User Details -->
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Full Name</div>
                                    <div class="col-lg-9 col-md-8">{{ $profile->first_name }} {{ $profile->middle_name ? $profile->middle_name[0].'.' : '' }} {{ $profile->last_name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">ID Number</div>
                                    <div class="col-lg-9 col-md-8">{{ $profile->id_number ?? 'N/A' }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 label">Email</div>
                                    <div class="col-lg-9 col-md-8">{{ $user->email }}</div>
                                </div>
                                @if($user->role_id == 3 || $user->role_id == 5) <!-- Faculty and Student Profile -->
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Course</div>
                                        <div class="col-lg-9 col-md-8">{{ $user->course->course_name ?? 'N/A' }}</div>
                                    </div>
                                @endif
                                @if($user->role_id == 5) <!-- Student Profile -->
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Skills</div>
                                        <div class="col-lg-9 col-md-8">
                                            @if($profile->skillTags->isEmpty())
                                                <p class="small fst-italic">No skills available.</p>
                                            @else
                                                {{ $profile->skillTags->pluck('name')->implode(', ') }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 label">Curriculum Vitae</div>
                                        <div class="col-lg-9 col-md-8">
                                            @if ($profile->cv_file_path)
                                                <button class="btn btn-primary" onclick="showPreview('{{ route('profile.previewCV', $profile->id) }}')"><i class="bi bi-folder"></i></button>
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>

                                    <h5 class="card-title mt-4">Internship</h5>
                                    @if ($acceptedInternship)
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Company Name</div>
                                            <div class="col-lg-9 col-md-8">{{ $acceptedInternship->job->company->name }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 label">Position</div>
                                            <div class="col-lg-9 col-md-8">{{ $acceptedInternship->job->title }}</div>
                                        </div>
                                    @else
                                        <p>No Internship Yet, Please Apply or Wait for Acceptance</p>
                                    @endif
                                @endif
                            @endif

                            <h5 class="card-title mt-4">Links</h5>
                            @if($profile->links->isEmpty())
                                <p class="small fst-italic">No links available.</p>
                            @else
                                <ul>
                                    @foreach($profile->links as $link)
                                        <li><a href="{{ $link->link_url }}" target="_blank">{{ $link->link_name }}</a></li>
                                    @endforeach
                                </ul>
                            @endif

                        </div>

                        <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                            @include('profile.partials.update-profile-form')
                        </div>

                        <!-- Change Password Tab with Success Message -->
                        <div class="tab-pane fade {{ session('status') === 'password-updated' || $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation') ? 'show active' : '' }} pt-3" id="profile-change-password">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div><!-- End Bordered Tabs -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal for CV preview -->
<div class="modal fade" id="cvPreviewModal" tabindex="-1" aria-labelledby="cvPreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cvPreviewLabel">File Preview</h5>
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

    document.getElementById('profilePicturePreview').addEventListener('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('previewProfilePictureModal'));
        modal.show();
    });
</script>

@endsection
