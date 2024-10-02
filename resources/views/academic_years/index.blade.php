@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Academic Years</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Academic Years</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<a href="{{ route('academic-years.create') }}" class="btn btn-primary mb-3">Add Academic Year</a>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Academic Year List</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($academicYears as $academicYear)
                    <tr>
                        <!-- Display the academic year using start_year and end_year -->
                        <td>
                            <a href="{{ route('academic-years.show', $academicYear->id) }}" class="btn btn-light btn-sm">
                                {{ $academicYear->start_year }} - {{ $academicYear->end_year }}
                            </a>
                        </td>
                        
                        <!-- Display the status badges -->
                        <td>
                            @if($academicYear->is_current)
                                <span class="badge bg-success">Current</span>
                            @else
                                <span class="badge bg-secondary">Non-Current</span>
                            @endif
                        </td>
                        
                        <td>
                            <!-- Edit Button -->
                            <a href="{{ route('academic-years.edit', $academicYear->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>

                            <!-- Set as Current Button (only show if it's not current) -->
                            @if(!$academicYear->is_current)
                                <form action="{{ route('academic-years.set-current', $academicYear->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-star"></i> Set as Current
                                    </button>
                                </form>
                            @endif

                            <!-- Deactivate Button (only show if it's current) -->
                            @if($academicYear->is_current)
                                <form action="{{ route('academic-years.deactivate', $academicYear->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Deactivate
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
