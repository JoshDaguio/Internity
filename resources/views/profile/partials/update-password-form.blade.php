<section>
    <form method="post" action="{{ route('password.update') }}">
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
            <button type="submit" class="btn btn-primary">{{ __('Change Password') }}</button>
        </div>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success mt-2">
                {{ __('Password updated successfully.') }}
            </div>
        @endif
    </form>
</section>
