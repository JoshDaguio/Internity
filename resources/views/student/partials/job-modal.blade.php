<div class="modal fade" id="jobModal-{{ $job->id }}" tabindex="-1" aria-labelledby="jobModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobModalLabel">{{ $job->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Company:</strong> {{ $job->company->name }}</p>
                <p><strong>Location:</strong> {{ $job->location }}</p>
                <p><strong>Work Type:</strong> {{ $job->work_type }}</p>
                <p><strong>Description:</strong> {{ $job->description }}</p>
                <p><strong>Qualification:</strong> {{ $job->qualification }}</p>
                <p><strong>Schedule:</strong> {{ json_decode($job->schedule, true)['start_time'] }} - {{ json_decode($job->schedule, true)['end_time'] }}</p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('internship.submitApplication', $job->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label>Endorsement Letter</label>
                        <input type="file" name="endorsement_letter" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </form>
            </div>
        </div>
    </div>
</div>
