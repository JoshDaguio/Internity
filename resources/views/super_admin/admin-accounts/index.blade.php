@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Admin Accounts</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Admin</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <a href="{{ route('admin-accounts.create') }}" class="btn btn-primary mb-3">Add Admin</a>

    <form method="GET" action="{{ route('admin-accounts.index') }}" class="mb-3">
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
            <h5 class="card-title">Admin Accounts List</h5>

            <!-- Table with DataTables formatting -->
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>ID Number</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                        <tr>
                            <td>
                                @if($admin->profile)
                                    {{ $admin->profile->first_name ?? 'N/A' }} {{ $admin->profile->last_name ?? '' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                @if($admin->profile)
                                    {{ $admin->profile->id_number ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                {{ $admin->status_id == 1 ? 'Active' : 'Inactive' }}
                            </td>
                            <td>
                                @if($admin->status_id == 1)
                                    <a href="{{ route('admin-accounts.edit', $admin) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $admin->id }}">
                                        Deactivate
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deactivateModal-{{ $admin->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deactivateModalLabel">Deactivate Admin Account</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to deactivate this admin account?
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('admin-accounts.destroy', $admin) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Deactivate</button>
                                                    </form>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('admin-accounts.reactivate', $admin) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">Reactivate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include DataTables scripts -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the DataTable
            const datatable = new simpleDatatables.DataTable(".datatable");
        });
    </script>
@endsection
