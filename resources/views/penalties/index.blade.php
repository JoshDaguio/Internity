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

    <a href="{{ route('penalties.create') }}" class="btn btn-primary mb-3 btn-sm">Add Penalty</a>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Penalties List</h5>
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Violation</th>
                            <th>Penalty Hours</th>
                            <th>Equivalent Days</th> <!-- Add this column -->
                            <th>Conditions</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penalties as $penalty)
                        <tr>
                            <td>
                                <a href="{{ route('penalties.show', $penalty) }}" class="btn btn-light btn-sm">
                                    {{ $penalty->violation }}
                                </a>
                            </td>
                            <td>{{ $penalty->penalty_hours }} hr/s</td>
                            <td>
                                @if($penalty->penalty_hours)
                                    {{ number_format($penalty->penalty_hours / 8) }} day/s
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $penalty->conditions ?? 'None' }}</td>
                            <td>
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
    </div>
@endsection
