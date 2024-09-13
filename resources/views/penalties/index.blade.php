@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Penalties</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Violation and Penalties</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <a href="{{ route('penalties.create') }}" class="btn btn-primary mb-3">Add Penalty</a>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Skill Tags List</h5>
            <table class="table datatable">
                <thead>
                    <tr>
                        <th scope="col">Violation</th>
                        <th scope="col">Penalty Hours</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penalties as $penalty)
                    <tr>
                        <td>{{ $penalty->violation }}</td>
                        <td>{{ $penalty->penalty_hours }}</td>
                        <td>
                            <a href="{{ route('penalties.show', $penalty) }}" class="btn btn-info btn-sm"><i class="bi bi-info-circle"></i></a>
                            <a href="{{ route('penalties.edit', $penalty) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('penalties.destroy', $penalty) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
