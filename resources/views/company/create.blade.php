@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Create Company Account</h1>

    <form action="{{ route('company.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Company</button>
    </form>
</div>
@endsection
