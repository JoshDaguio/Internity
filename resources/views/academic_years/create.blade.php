@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Create Academic Year</h1>
    <form action="{{ route('academic-years.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="start_year" class="form-label">Start Year</label>
            <select id="start_year" name="start_year" class="form-select" required>
                @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="end_year" class="form-label">End Year</label>
            <select id="end_year" name="end_year" class="form-select" required>
                @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('academic-years.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
