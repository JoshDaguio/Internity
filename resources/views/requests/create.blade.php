@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Create Excusal Request</h1>
    <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" name="subject" value="Excusal Request" readonly>
        </div>
        <div class="form-group mb-3">
            <label for="absence_date">Date of Absence</label>
            <input type="date" class="form-control" name="absence_date" required>
        </div>
        <div class="form-group mb-3">
            <label for="reason">Specify Reason</label>
            <textarea class="form-control" name="reason" rows="4" required></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="attachment">Attach File</label>
            <input type="file" class="form-control" name="attachment">
        </div>
        <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
</div>
@endsection
