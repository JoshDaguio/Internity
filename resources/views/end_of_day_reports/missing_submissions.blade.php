@extends('layouts.app')

@section('body')
    <h1>Missing Submissions for {{ \Carbon\Carbon::create()->month($selectedMonth)->format('F') }}</h1>

    @if($missingDates->isEmpty())
        <p>No missing submissions. Great job!</p>
    @else
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($missingDates as $date)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($date)->format('F d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
