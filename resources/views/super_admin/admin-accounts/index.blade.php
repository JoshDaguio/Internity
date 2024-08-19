@extends('layouts.app')

@section('body')
    <h1>Admin Accounts</h1>
    <a href="{{ route('admin-accounts.create') }}" class="btn btn-primary">Create New Admin</a>
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
                            {{ $admin->profile->first_name }} {{ $admin->profile->last_name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $admin->email }}</td>
                    <td>
                        @if($admin->profile)
                            {{ $admin->profile->id_number }}
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
                            <form action="{{ route('admin-accounts.destroy', $admin) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Deactivate</button>
                            </form>
                        @else
                            <form action="{{ route('admin-accounts.reactivate', $admin) }}" method="POST" style="display:inline-block;">
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
