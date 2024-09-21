<section>
    <form id="password-update-form" method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <!-- Current Password Field -->
        <div class="row mb-3">
            <label for="update_password_current_password" class="col-md-4 col-lg-3 col-form-label">{{ __('Current Password') }}</label>
            <div class="col-md-8 col-lg-9">
                <div class="input-group">
                    <input id="update_password_current_password" name="current_password" type="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#update_password_current_password">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <!-- Custom error message for incorrect current password -->
                @error('current_password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- New Password Field -->
        <div class="row mb-3">
            <label for="update_password_password" class="col-md-4 col-lg-3 col-form-label">{{ __('New Password') }}</label>
            <div class="col-md-8 col-lg-9">
                <div class="input-group">
                    <input id="update_password_password" name="password" type="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#update_password_password">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <!-- Custom error messages for password format validation -->
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Confirm Password Field -->
        <div class="row mb-3">
            <label for="update_password_password_confirmation" class="col-md-4 col-lg-3 col-form-label">{{ __('Confirm Password') }}</label>
            <div class="col-md-8 col-lg-9">
                <div class="input-group">
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#update_password_password_confirmation">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <!-- Custom error message for password confirmation mismatch -->
                @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary">{{ __('Change Password') }}</button>
        </div>

        <!-- Success message after password is updated -->
        @if (session('status') === 'password-updated')
            <div class="alert alert-success mt-2">
                {{ __('Password updated successfully.') }}
            </div>
        @endif
    </form>
</section>

<!-- JavaScript for toggling password visibility -->
<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-target'));
            const icon = this.querySelector('i');

            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                target.type = 'password';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });
    });
</script>
