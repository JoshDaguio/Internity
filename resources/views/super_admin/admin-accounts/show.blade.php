@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Admin Account Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="{{ route('admin-accounts.index') }}">Admin</a></li>
            <li class="breadcrumb-item active">Admin Details</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Admin Details</h5>

        <div class="row">
            <div class="col-md-4"><strong>Full Name:</strong></div>
            <div class="col-md-8">{{ $admin->profile->first_name }} {{ $admin->profile->last_name }}</div>
        </div>
        <div class="row">
            <div class="col-md-4"><strong>Email:</strong></div>
            <div class="col-md-8">{{ $admin->email }}</div>
        </div>
        <div class="row">
            <div class="col-md-4"><strong>ID Number:</strong></div>
            <div class="col-md-8">{{ $admin->profile->id_number ?? 'N/A' }}</div>
        </div>
        <div class="row">
            <div class="col-md-4"><strong>Status:</strong></div>
            <div class="col-md-8">{{ $admin->status_id == 1 ? 'Active' : 'Inactive' }}</div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <a href="{{ route('admin-accounts.edit', $admin) }}" class="btn btn-warning"><i class="bi bi-pencil"></i></a>

                <!-- Show the Deactivate button only if the account is active -->
                    @if($admin->status_id == 1)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $admin->id }}">
                            Deactivate
                        </button>

                        <!-- Deactivate Modal -->
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
                    @endif

                    <!-- Show the Reactivate button only if the account is inactive -->
                    @if($admin->status_id == 2)
                        <form action="{{ route('admin-accounts.reactivate', $admin) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">Reactivate</button>
                        </form>
                    @endif


                <a href="{{ route('admin-accounts.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Activity Logs</h5>
        <div class="table-responsive">

            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Changes</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ ucfirst($log->action) }}</td>
                            <td>{{ ucfirst($log->target) }}</td>
                            <td>
                                @if($log->action == 'Updated Student' || $log->action == 'Updated Company' || $log->action == 'Updated Faculty' || $log->action == 'Updated Course' || $log->action == 'Updated File')
                                    @php
                                        $changes = json_decode($log->changes, true);
                                    @endphp

                                    @if (!empty($changes))
                                        <ul>
                                            @foreach ($changes as $field => $change)
                                                @if(is_array($change))
                                                    <li>{{ ucfirst($field) }}: from <strong>{{ $change['old'] }}</strong> to <strong>{{ $change['new'] }}</strong></li>
                                                @else
                                                    <li>{{ ucfirst($field) }}: {{ $change }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        No specific changes recorded.
                                    @endif
                                @else
                                    No changes available
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('F d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>


            </table>
        </div>
    </div>
</div>
@endsection
