@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Edit Academic Year</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">S.Y. Configuration</li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
</div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Edit Academic Year Form</h5>

            <form action="{{ route('academic-years.update', $academicYear) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PATCH')
                <div class="row g-3">
                    <!-- Start Year -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="start_year" name="start_year" class="form-select" required>
                                <option value="" disabled>Select Start Year</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $year == $academicYear->start_year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                            <label for="start_year">Start Year</label>
                            <div class="invalid-feedback">Please select a start year.</div>
                        </div>
                    </div>

                    <!-- End Year -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="end_year" name="end_year" class="form-select" required>
                                <option value="" disabled>Select End Year</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $year == $academicYear->end_year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                            <label for="end_year">End Year</label>
                            <div class="invalid-feedback">Please select an end year.</div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('academic-years.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

<script>
    // JavaScript for Bootstrap validation
    (function () {
        'use strict'

        const forms = document.querySelectorAll('.needs-validation')

        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
