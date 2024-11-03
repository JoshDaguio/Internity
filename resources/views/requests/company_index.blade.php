@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Excusal Requests</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Requests</li>
                <li class="breadcrumb-item active">Student Request</li>
            </ol>
        </nav>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Filter by Academic Year</h5>
            <!-- Academic Year Filter -->
            <form method="GET" action="{{ route('requests.companyIndex') }}">
                <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->start_year }} - {{ $year->end_year }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Requests</h5>
            <div class="table-responsive">
                @if($requests->isEmpty())
                    <p class="text-center">No approved requests found.</p>
                @else
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Excuse Detail</th>
                                <th>Date of Absence</th>
                                <th>Status</th>
                                <th>Attached File</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->student->profile->first_name }} {{ $request->student->profile->last_name }}</td>
                                    <td>{{ $request->subject }}</td>
                                    <td>{{ $request->reason }}</td>
                                    <td>{{ \Carbon\Carbon::parse($request->absence_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if ($request->attachment_path)
                                            <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#attachmentModal-{{ $request->id }}">
                                                <i class="bi bi-folder"></i>
                                            </button>
                                        @else
                                            <p><code>No file attached</code></p>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($request->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('requests.companyShow', $request->id) }}" class="btn btn-primary btn-sm">View</a>
                                    </td>
                                </tr>

                                 <!-- Modal for Attachment Preview for each request -->
                                <div class="modal fade" id="attachmentModal-{{ $request->id }}" tabindex="-1" aria-labelledby="attachmentModalLabel-{{ $request->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="attachmentModalLabel-{{ $request->id }}">Attachment Preview</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <iframe src="{{ route('requests.preview', $request->id) }}" style="width:100%; height:600px;" frameborder="0"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>


    <script>
        function showPreview(url) {
            const modal = new bootstrap.Modal(document.getElementById('attachmentModal'), {});
            document.querySelector('#attachmentModal iframe').src = url;
            modal.show();
        }
    </script>
@endsection
