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

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Evaluations Available to You</h5>

            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr>
                                <td>{{ $evaluation->title }}</td>
                                <td>{{ $evaluation->description }}</td>
                                <td>{{ ucfirst($evaluation->evaluation_type) }}</td>
                                <td>
                                    @if($evaluation->responses->where('user_id', auth()->id())->count() > 0)
                                        Answered
                                    @else
                                        Not Answered
                                    @endif
                                </td>
                                <td>
                                    @if($evaluation->responses->where('user_id', auth()->id())->count() == 0)
                                        <a href="{{ route('evaluations.showResponseForm', $evaluation->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-file-earmark-text"></i> Answer</a>
                                    @else
                                        <span class="badge bg-success">Already Answered</span>
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
