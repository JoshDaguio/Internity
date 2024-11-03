@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Pullout Requests</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Requests</li>
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
            <h5 class="card-title">Filter by Academic Year</h5>
            <form method="GET" action="{{ route('pullouts.companyIndex') }}">
                <div class="row">
                    <!-- Academic Year Filter -->
                    <div class="col-md-12">
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                    {{ $year->start_year }} - {{ $year->end_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Pullout Requests Recieved</h5>
            @if($pullouts->isEmpty())
                <p class="text-center">No pullout requests found.</p>
            @else
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>Student(s)</th>
                                <th>Sent By</th>
                                <th>Pullout Date</th>
                                <th>Excuse Detail</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pullouts as $index => $pullout)
                                <tr>
                                    <td>
                                        <ul>
                                            @foreach($pullout->students as $student)
                                                <li>{{ $student->profile->first_name }} {{ $student->profile->last_name }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>{{ $pullout->creator->profile->first_name }} {{ $pullout->creator->profile->last_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pullout->pullout_date)->format('F d, Y') }}</td>
                                    <td>{{ $pullout->excuse_detail }}</td>
                                    <td>
                                        @if($pullout->status === 'accepted')
                                            <span class="badge bg-success">Accepted</span>
                                        @elseif($pullout->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($pullout->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pullout->status === 'pending')
                                            <a href="{{ route('pullouts.showRespondForm', $pullout) }}" class="btn btn-primary btn-sm">Respond</a>
                                        @else
                                            <span class="badge bg-success">Handled</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection
