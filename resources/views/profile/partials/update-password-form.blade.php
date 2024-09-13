<section>
    <form id="password-update-form" method="post" action="{{ route('password.update') }}" novalidate>
        @csrf
        @method('put')

        <div class="row mb-3">
            <label for="update_password_current_password" class="col-md-4 col-lg-3 col-form-label">{{ __('Current Password') }}</label>
            <div class="col-md-8 col-lg-9">
                <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
                @error('current_password')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="update_password_password" class="col-md-4 col-lg-3 col-form-label">{{ __('New Password') }}</label>
            <div class="col-md-8 col-lg-9">
                <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
                @error('password')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="update_password_password_confirmation" class="col-md-4 col-lg-3 col-form-label">{{ __('Confirm Password') }}</label>
            <div class="col-md-8 col-lg-9">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                @error('password_confirmation')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="text-center">
            <!-- Button to trigger the modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmPasswordChangeModal">
                {{ __('Change Password') }}
            </button>
        </div>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success mt-2">
                {{ __('Password updated successfully.') }}
            </div>
        @endif
    </form>
</section>

<!-- Modal -->
<div class="modal fade" id="confirmPasswordChangeModal" tabindex="-1" aria-labelledby="confirmPasswordChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmPasswordChangeModalLabel">Confirm Password Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to change your password?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirm-change-password">Confirm</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('confirm-change-password').addEventListener('click', function () {
        document.getElementById('password-update-form').submit();
    });
</script>
