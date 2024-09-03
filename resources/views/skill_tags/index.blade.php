@extends('layouts.app')

@section('body')
    <h1>Skill Tags</h1>

    <!-- Button to trigger the modal for adding a new skill tag -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createSkillTagModal">
        Add Skill Tag
    </button>

    <!-- Skill Tags Table -->
    @if($skillTags->isEmpty())
        <p>No skill tags added yet.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Skill Tag</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($skillTags as $tag)
                    <tr>
                        <td>{{ $tag->name }}</td>
                        <td>
                            <!-- Edit Button -->
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editSkillTagModal-{{ $tag->id }}">
                                Edit
                            </button>

                            <!-- Delete Form -->
                            <form action="{{ route('skill_tags.destroy', $tag->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>

                            <!-- Modal for Editing Skill Tag -->
                            <div class="modal fade" id="editSkillTagModal-{{ $tag->id }}" tabindex="-1" aria-labelledby="editSkillTagModalLabel-{{ $tag->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editSkillTagModalLabel-{{ $tag->id }}">Edit Skill Tag</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('skill_tags.update', $tag->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="mb-3">
                                                    <label for="editTagName-{{ $tag->id }}" class="form-label">Skill Tag Name</label>
                                                    <input type="text" id="editTagName-{{ $tag->id }}" name="name" class="form-control" value="{{ $tag->name }}" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Update Skill Tag</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Modal for Creating Skill Tag -->
    <div class="modal fade" id="createSkillTagModal" tabindex="-1" aria-labelledby="createSkillTagModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSkillTagModalLabel">Add Skill Tag</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('skill_tags.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="tagName" class="form-label">Skill Tag Name</label>
                            <input type="text" id="tagName" name="name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Skill Tag</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection