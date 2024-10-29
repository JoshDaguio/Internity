@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Create Pullout Request</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Requests</li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </nav>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Create New Pullout Request</h5>
            <form method="POST" action="{{ route('pullouts.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="company" class="form-label">Select Company</label>
                    <select name="company_id" id="company" class="form-select" required>
                        <option value="">Choose a company...</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="students" class="form-label">Select Students</label>
                    <select name="students[]" id="students" class="form-select" multiple required>
                        <!-- Dynamically populated based on selected company -->
                    </select>
                </div>

                <div class="mb-3">
                    <label for="pullout_date" class="form-label">Pullout Date</label>
                    <input type="date" name="pullout_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="excuse_detail" class="form-label">Excuse Detail</label>
                    <textarea name="excuse_detail" id="excuse_detail" class="form-control" rows="4" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Send Request</button>
                <a href="{{ route('pullouts.index') }}" class="btn btn-secondary btn-sm">Cancel</a>

            </form>
        </div>
    </div>


<!-- Include Select2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#students').select2({
            placeholder: 'Select students...',
            width: '100%',
        });

        $('#company').on('change', function () {
            const companyId = $(this).val();
            if (companyId) {
                $.ajax({
                    url: '/api/company/' + companyId + '/students',
                    method: 'GET',
                    success: function (data) {
                        let studentOptions = '';
                        data.forEach(function (student) {
                            studentOptions += `<option value="${student.id}">${student.name}</option>`;
                        });
                        $('#students').html(studentOptions);
                    }
                });
            } else {
                $('#students').html('');
            }
        });
    });
</script>
@endsection
