@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Penalties</h1>
    <a href="{{ route('penalties.create') }}" class="btn btn-primary mb-3">Add Penalty</a>
    <table class="table table-striped">
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
@endsection
