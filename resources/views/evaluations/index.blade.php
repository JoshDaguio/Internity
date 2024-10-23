@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Evaluations</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Evaluations</li>
            </ol>
        </nav>
    </div>

    <!-- Flash message for success -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Evaluations</h5>

            <a href="{{ route('evaluations.create') }}" class="btn btn-success btn-sm mb-3"><i class="bi bi-plus-circle"></i> Create</a>

            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Creator</th>
                            <th>Results</th>
                            <th>Test</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr>
                                <td>{{ $evaluation->title }}</td>
                                <td>{{ $evaluation->description }}</td>
                                <td>{{ ucfirst($evaluation->evaluation_type) }}</td>
                                <td>{{ $evaluation->creator->profile->first_name}} {{ $evaluation->creator->profile->last_name}}</td>
                                <td>
                                    <a href="{{ route('evaluations.results', $evaluation->id) }}" class="btn btn-info btn-sm"><i class="bi bi-bar-chart-line"></i></a>
                                </td>
                                <td>
                                    <a href="{{ route('evaluations.showResponseForm', $evaluation->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-file-earmark-text"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
