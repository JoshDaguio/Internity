@extends('layouts.app')

@section('body')
    <h1>File Uploads</h1>

    @if(Auth::user()->role_id != 5)  <!-- This condition hides the button for students -->
        <a href="{{ route('file_uploads.create') }}" class="btn btn-primary mb-3">Upload File</a>

        <!-- Filter Section -->
        <form method="GET" action="{{ route('file_uploads.index') }}" class="mb-3">
            <div class="d-flex">
                <select name="filter" class="form-select me-2" onchange="this.form.submit()">
                    <option value="">All Files</option>
                    <option value="my_uploads" {{ request('filter') == 'my_uploads' ? 'selected' : '' }}>My Uploads</option>
                    <option value="other_uploads" {{ request('filter') == 'other_uploads' ? 'selected' : '' }}>Other Uploads</option>
                </select>
            </div>
        </form>
    @endif

    @if($files->isEmpty())
        <p>No files uploaded yet.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Description</th>
                    <th>Date Uploaded</th>
                    <th>Uploaded By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                    <tr>
                        <td>{{ $file->file_name }}</td>
                        <td>{{ $file->description }}</td>
                        <td>{{ $file->created_at->format('F d, Y') }}</td>
                        <td>
                            @if($file->uploader->profile)
                                {{ $file->uploader->profile->first_name }} {{ $file->uploader->profile->last_name }}
                            @else
                                {{ $file->uploader->name }}
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-dark" onclick="showPreview('{{ route('file_uploads.preview', $file->id) }}')"><i class="bi bi-folder"></i></button>
                            <a href="{{ route('file_uploads.download', $file->id) }}" class="btn btn-success"><i class="bi bi-download"></i></a>
                            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                <form action="{{ route('file_uploads.destroy', $file->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Modal for file preview -->
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
