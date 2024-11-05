@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Leave Requests</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Requests</li>
                <li class="breadcrumb-item active">Leave Requests</li>
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
            <form method="GET" action="{{ route('pullouts.index') }}">
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
            <h5 class="card-title">Leave Requests Details</h5>

            <a href="{{ route('pullouts.create') }}" class="btn btn-success btn-sm mb-3"><i class="bi bi-plus-circle"></i> Create</a>

            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Student(s)</th>
                            <th>Created By</th>
                            <th>Pullout Date</th>
                            <th>Excuse Detail</th>
                            <th>Status</th>
                            <th>Company Remark</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pullouts as $index => $pullout)
                            <tr>
                                <td>{{ $pullout->company->name ?? 'N/A' }}</td>
                                <td>
                                    @foreach ($pullout->students as $student)
                                        <div>{{ $student->profile->first_name ?? 'N/A' }} {{ $student->profile->last_name ?? 'N/A' }}</div>
                                    @endforeach
                                </td>
                                <td>{{ $pullout->creator->profile->first_name ?? 'N/A' }} {{ $pullout->creator->profile->last_name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($pullout->pullout_date)->format('M d, Y') }}</td>
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
                                    @if($pullout->company_remark)
                                        {{ $pullout->company_remark }}
                                    @else
                                        <span class="badge bg-warning">Pending</span>
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
