@extends('layouts.app')

@section('body')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Registration Successful') }}</div>

                    <div class="card-body text-center">
                        <h5>Thank you for registering with us!</h5>
                        <p>Your account request has been successfully submitted. Please await an email confirmation regarding the approval of your account registration.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
