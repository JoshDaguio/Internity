@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Create Excusal Request</h1>
        <nav>
            <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Requests</li>
            <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Create Request</h5>
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

                <button type="submit" class="btn btn-primary btn-sm">Submit Request</button>
                <a href="{{ route('requests.studentIndex') }}" class="btn btn-secondary btn-sm">Cancel</a>

            </form>
        </div>
    </div>
@endsection
