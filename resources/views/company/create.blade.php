@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Create Company Account</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('company.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Company Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contact Person</label>
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="first_name" class="form-control" id="first_name" value="{{ old('first_name') }}" placeholder="First Name" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="last_name" class="form-control" id="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required>
                </div>
            </div>
        </div>


        <button type="submit" class="btn btn-primary">Create Company</button>
    </form>
</div>
@endsection
