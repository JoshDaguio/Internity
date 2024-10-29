@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Penalty and Violation Detail</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Violation and Penalties</li>
                <li class="breadcrumb-item active">{{ $penalty->violation }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <a href="{{ route('penalties.index') }}" class="btn btn-secondary mb-3 btn-sm">Back</a>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Penalties Details</h5>
            <p><strong>Violation:</strong> {{ $penalty->violation }}</p>
            <p><strong>Penalty Hours:</strong> {{ $penalty->penalty_hours }}</p>
            <p><strong>Equivalent Days:</strong> {{ number_format($penalty->penalty_hours / 8) ?? 'N/A'}} day/s </p>
            <p><strong>Condition:</strong> {{ $penalty->conditions ?? 'None' }} </p>
            <a href="{{ route('penalties.edit', $penalty) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
            <!-- <form action="{{ route('penalties.destroy', $penalty) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Delete</button>
            </form> -->
        </div>
    </div>

@endsection
