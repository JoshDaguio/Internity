@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Company Details</h1>

    <table class="table table-bordered">
        <tr>
            <th>Company Name:</th>
            <td>{{ $company->name }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $company->email }}</td>
        </tr>
        <tr>
            <th>Contact Person:</th>
            <td>{{ $company->profile->first_name }} {{ $company->profile->last_name }}</td>
        </tr>
        <tr>
            <th>Status:</th>
            <td>{{ $company->status_id == 1 ? 'Active' : 'Inactive' }}</td>
        </tr>
    </table>

    <a href="{{ route('company.edit', $company->id) }}" class="btn btn-warning {{ $company->status_id != 1 ? 'd-none' : '' }}">Edit</a>
    <a href="{{ route('company.index') }}" class="btn btn-secondary">Back to List</a>

    @if($company->status_id == 1)
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal">
            Deactivate
        </button>
    @else
        <form action="{{ route('company.reactivate', $company->id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">Reactivate</button>
        </form>
    @endif

    <!-- Deactivate Modal -->
    <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deactivateModalLabel">Deactivate Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to deactivate this company account?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('company.destroy', $company->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Deactivate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
