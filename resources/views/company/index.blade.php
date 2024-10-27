@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Company Accounts</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Company</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<a href="{{ route('company.create') }}" class="btn btn-primary mb-3 btn-sm">Add Company</a>
<a href="{{ route('company.import') }}" class="btn btn-success mb-3 ms-2 btn-sm">Import Companies</a>


<form method="GET" action="{{ route('company.index') }}" class="mb-3">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Filter by Status</h5>
            <div class="row">
                <div class="d-flex">
                    <select name="status_id" id="status_id" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status_id') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ request('status_id') == '2' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Partner Company List</h5>
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Contact Person</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <!-- <th>Actions</th> -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                        <tr>
                            <td>
                                <a href="{{ route('company.show', $company->id) }}" class="btn btn-light btn-sm">
                                    {{ $company->name }}
                                </a>
                            </td>
                            <td>{{ $company->email }}</td>
                            <td>{{ $company->profile->first_name ?? 'N/A' }} {{ $company->profile->last_name ?? '' }}</td>
                            <td>{{ $company->expiry_date ? \Carbon\Carbon::parse($company->expiry_date)->toFormattedDateString() : 'N/A' }}</td>
                            <td>
                                @if($company->status_id == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <!-- <td>
                                @if($company->status_id == 1)
                                    <a href="{{ route('company.edit', $company->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $company->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <form action="{{ route('company.reactivate', $company->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                @endif
                            </td> -->
                        </tr>

                        
                        <!-- <div class="modal fade" id="deactivateModal-{{ $company->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel-{{ $company->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deactivateModalLabel-{{ $company->id }}">Deactivate Company</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to deactivate this company account?
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('company.destroy', $company->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Deactivate</button>
                                        </form>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>  
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable on the table element
        $('.datatable').DataTable({
            paging: true,           // Enable pagination
            pageLength: 10,         // Number of records to show per page
            searching: true,        // Enable search functionality
            lengthChange: true,     // Enable user to change number of rows displayed
            order: [[0, 'asc']],    // Sort by the first column by default
        });
    });
</script>

@endsection
