@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Monthy EOD Reports</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Intenrship Files</li>
                <li class="breadcrumb-item active">Monthly Reports</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('internship.files') }}" class="btn btn-secondary mb-3 btn-sm">Back</a>


    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Submitted Monthly End of Day Reports</h5>
            <ul class="list-group">
                @foreach($eodReports as $report)
                    <li class="list-group-item">
                        {{ \Carbon\Carbon::parse($report->month_year)->format('F Y') }}
                        <button onclick="showPreview('{{ route('internship.files.preview', ['type' => 'eod', 'id' => $report->id]) }}')" class="btn btn-dark btn-sm float-end">Preview</button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

<!-- Modal for File Preview -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewLabel">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="filePreviewIframe" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    function showPreview(url) {
        document.getElementById('filePreviewIframe').src = url;
        var filePreviewModal = new bootstrap.Modal(document.getElementById('filePreviewModal'), {});
        filePreviewModal.show();
    }
</script>

@endsection
