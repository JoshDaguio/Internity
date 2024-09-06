<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="row mb-3">
        <label for="profile_picture" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
        <div class="col-md-8 col-lg-9">
            <img id="profilePicturePreview" src="{{ $profile->profile_picture ? Storage::url($profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle">
            <div class="pt-2">
                <input type="file" name="profile_picture" class="form-control mt-2" onchange="previewProfilePicture(event)">
                @if($profile->profile_picture)
                    <small class="text-muted">Current Image: <a href="{{ Storage::url($profile->profile_picture) }}" target="_blank">View</a></small>
                @endif
            </div>
        </div>
    </div>


    @if($user->role_id == 4) <!-- Company Profile -->
        <div class="row mb-3">
            <label for="name" class="col-md-4 col-lg-3 col-form-label">Company Name</label>
            <div class="col-md-8 col-lg-9">
                <input name="name" type="text" class="form-control" id="name" value="{{ old('name', $user->name) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="first_name" class="col-md-4 col-lg-3 col-form-label">Contact Person First Name</label>
            <div class="col-md-8 col-lg-9">
                <input name="first_name" type="text" class="form-control" id="first_name" value="{{ old('first_name', $profile->first_name) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="last_name" class="col-md-4 col-lg-3 col-form-label">Contact Person Last Name</label>
            <div class="col-md-8 col-lg-9">
                <input name="last_name" type="text" class="form-control" id="last_name" value="{{ old('last_name', $profile->last_name) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="contact_number" class="col-md-4 col-lg-3 col-form-label">Contact Number</label>
            <div class="col-md-8 col-lg-9">
                <input name="contact_number" type="text" class="form-control" id="contact_number" value="{{ old('contact_number', $profile->contact_number) }}">
            </div>
        </div>

        <div class="row mb-3">
            <label for="address" class="col-md-4 col-lg-3 col-form-label">Address</label>
            <div class="col-md-8 col-lg-9">
                <input name="address" type="text" class="form-control" id="address" value="{{ old('address', $profile->address) }}">
            </div>
        </div>
    @else <!-- Common Fields for Student, Super Admin, Admin, and Faculty -->
        <div class="row mb-3">
            <label for="first_name" class="col-md-4 col-lg-3 col-form-label">First Name</label>
            <div class="col-md-8 col-lg-9">
                <input name="first_name" type="text" class="form-control" id="first_name" value="{{ old('first_name', $profile->first_name) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="middle_name" class="col-md-4 col-lg-3 col-form-label">Middle Name</label>
            <div class="col-md-8 col-lg-9">
                <input name="middle_name" type="text" class="form-control" id="middle_name" value="{{ old('middle_name', $profile->middle_name) }}">
            </div>
        </div>

        <div class="row mb-3">
            <label for="last_name" class="col-md-4 col-lg-3 col-form-label">Last Name</label>
            <div class="col-md-8 col-lg-9">
                <input name="last_name" type="text" class="form-control" id="last_name" value="{{ old('last_name', $profile->last_name) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <label for="id_number" class="col-md-4 col-lg-3 col-form-label">ID Number</label>
            <div class="col-md-8 col-lg-9">
                <input name="id_number" type="text" class="form-control" id="id_number" value="{{ old('id_number', $profile->id_number) }}">
            </div>
        </div>

        <div class="row mb-3">
            <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
            <div class="col-md-8 col-lg-9">
                <textarea name="about" class="form-control" id="about" style="height: 100px">{{ old('about', $profile->about) }}</textarea>
            </div>
        </div>

        @if($user->role_id == 3 || $user->role_id == 5) <!-- Faculty and Student Profile -->
            <div class="row mb-3">
                <label for="course" class="col-md-4 col-lg-3 col-form-label">Course</label>
                <div class="col-md-8 col-lg-9">
                    <input type="text" class="form-control" id="course" value="{{ $user->course->course_name }}" disabled>
                </div>
            </div>
        @endif

        @if($user->role_id == 5) <!-- Student Profile -->

            <div class="row mb-3">
                <label for="skill_tags" class="col-md-4 col-lg-3 col-form-label">Skills</label>
                <div class="col-md-8 col-lg-9">
                    <select name="skill_tags[]" id="skill_tags" class="form-control" multiple="multiple">
                        @foreach($skillTags as $skillTag)
                            <option value="{{ $skillTag->id }}" 
                                {{ in_array($skillTag->id, $profile->skillTags->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $skillTag->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Select your skills or type to search and select.</small>
                </div>
            </div>

            <div class="row mb-3">
                <label for="cv" class="col-md-4 col-lg-3 col-form-label">Curriculum Vitae</label>
                <div class="col-md-8 col-lg-9">
                    <input type="file" name="cv" class="form-control" id="cv">
                    @if($profile->cv_file_path)
                        <small class="text-muted">Current CV: <a href="{{ route('profile.previewCV', $profile->id) }}" target="_blank">Preview</a></small>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <!-- Links -->
    <div class="row mb-3">
        <label for="links" class="col-md-4 col-lg-3 col-form-label">Links</label>
        <div class="col-md-8 col-lg-9">
            @foreach($profile->links as $link)
                <div class="input-group mb-2">
                    <input type="text" name="link_names[]" class="form-control" placeholder="Link Name" value="{{ old('link_name', $link->link_name) }}">
                    <input type="text" name="link_urls[]" class="form-control" placeholder="Link URL" value="{{ old('link_url', $link->link_url) }}">
                </div>
            @endforeach
            <!-- Add new link -->
            <div id="newLink"></div>
            <button type="button" class="btn btn-secondary" onclick="addNewLink()">Add Another Link</button>
        </div>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>

<script>
    function addNewLink() {
        var newLink = `<div class="input-group mb-2">
                           <input type="text" name="link_names[]" class="form-control" placeholder="Link Name">
                           <input type="text" name="link_urls[]" class="form-control" placeholder="Link URL">
                       </div>`;
        document.getElementById('newLink').insertAdjacentHTML('beforeend', newLink);
    }

    function previewProfilePicture(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('profilePicturePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    $(document).ready(function() {
        $('#skill_tags').select2({
            placeholder: 'Select skills',
            allowClear: true,
            width: '100%'  // Makes the select box fit the container width
        });
    });

</script>
