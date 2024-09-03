@extends('layouts.app')

@section('body')
    <h1>Admin Accounts</h1>
    <a href="{{ route('admin-accounts.create') }}" class="btn btn-primary mb-3">Create New Admin</a>

    <!-- Filter Section -->
    <form method="GET" action="{{ route('admin-accounts.index') }}" class="mb-3">
        <div class="d-flex">
            <select name="status" class="form-select me-2" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>

    <table class="table">
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
                            <a href="{{ route('admin-accounts.edit', $admin) }}" class="btn btn-warning">Edit</a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $admin->id }}">
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
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('admin-accounts.destroy', $admin) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Deactivate</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <form action="{{ route('admin-accounts.reactivate', $admin) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Reactivate</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
