@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Company List</h1>

    <a href="{{ route('company.create') }}" class="btn btn-primary mb-3">Add Company</a>

    <!-- Filter by Status -->
    <form method="GET" action="{{ route('company.index') }}" class="mb-3">
        <div class="d-flex">
            <select name="status_id" id="status_id" class="form-select me-2" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="1" {{ request('status_id') == '1' ? 'selected' : '' }}>Active</option>
                <option value="2" {{ request('status_id') == '2' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Company Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->email }}</td>
                    <td>{{ $company->status_id == 1 ? 'Active' : 'Inactive' }}</td>
                    <td>
                        @if($company->status_id == 1)
                            <a href="{{ route('company.show', $company->id) }}" class="btn btn-info"><i class="bi bi-info-circle"></i></a>
                            <a href="{{ route('company.edit', $company->id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $company->id }}">
                                Deactivate
                            </button>
                        @else
                            <form action="{{ route('company.reactivate', $company->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Reactivate</button>
                            </form>
                        @endif
                    </td>
                </tr>

                <!-- Deactivate Modal -->
                <div class="modal fade" id="deactivateModal-{{ $company->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel-{{ $company->id }}" aria-hidden="true">
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
                </div>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
