@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Manage Questions for {{ $evaluation->title }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Evaluations</li>
                <li class="breadcrumb-item active">Manage Questions</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('evaluations.updateQuestions', $evaluation->id) }}" method="POST">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Existing Questions</h5>
                <div id="questions">
                    @foreach($evaluation->questions as $index => $question)
                        <div class="form-group mb-3" id="existing_question_{{ $question->id }}">
                            <label class="mb-2"><strong>Question {{ $index + 1 }}</strong></label>
                            <input type="text" name="questions[{{ $question->id }}][text]" value="{{ $question->question_text }}" class="form-control mb-2" required>
                            <input type="hidden" name="questions[{{ $question->id }}][type]" value="{{ $question->question_type }}">

                            @if ($question->question_type === 'radio')
                                <div class="d-flex justify-content-center mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" disabled>
                                        <label class="form-check-label">1</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" disabled>
                                        <label class="form-check-label">2</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" disabled>
                                        <label class="form-check-label">3</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" disabled>
                                        <label class="form-check-label">4</label>
                                    </div>
                                </div>
                            @else
                                <textarea class="form-control mt-2" rows="3" disabled>Text Question</textarea>
                            @endif

                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-existing-question" data-question-id="{{ $question->id }}">Remove</button>
                        </div>
                    @endforeach
                </div>

                <h5 class="card-title mt-4">Add New Questions</h5>
                <div id="new-questions">
                    <!-- Dynamic question area for new questions -->
                </div>
                <div class="col-md-12 text-center">
                    <button type="button" id="addRadioQuestion" class="btn btn-success btn-sm mb-3"><i class="bi bi-plus-circle"></i> Radio Question</button>
                    <button type="button" id="addCommentQuestion" class="btn btn-success btn-sm mb-3 me-3"><i class="bi bi-plus-circle"></i> Text Question</button>
                </div>

                <input type="hidden" name="delete_questions" id="delete-questions" value="">

                <button type="submit" class="btn btn-primary mt-4">Save Changes</button>
                <a href="{{ route('evaluations.index') }}" class="btn btn-secondary mt-4">Cancel</a>
            </div>
        </div>
    </form>

    <script>
        // Function to add dynamic new questions
        document.getElementById('addCommentQuestion').addEventListener('click', function() {
            addQuestion('long_text', 'Text Question');
        });

        document.getElementById('addRadioQuestion').addEventListener('click', function() {
            addQuestion('radio', 'Radio Button');
        });

        function addQuestion(type, label) {
            const newQuestionsDiv = document.getElementById('new-questions');
            const questionCount = newQuestionsDiv.children.length;

            let questionHTML = `
                <div class="form-group mb-3" id="new_question_${questionCount}">
                    <label class="mb-2"><strong>Question ${questionCount + 1}</strong></label>
                    <input type="text" name="new_questions[${questionCount}][text]" class="form-control mb-2" required placeholder="Enter question text">
                    <input type="hidden" name="new_questions[${questionCount}][type]" value="${type}">
                    <label>${label}</label>`;

            if (type === 'radio') {
                questionHTML += `
                    <div class="d-flex justify-content-center mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" disabled>
                            <label class="form-check-label">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" disabled>
                            <label class="form-check-label">2</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" disabled>
                            <label class="form-check-label">3</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" disabled>
                            <label class="form-check-label">4</label>
                        </div>
                    </div>`;
            } else if (type === 'long_text') {
                questionHTML += `
                    <textarea class="form-control" rows="3" placeholder="Text Question" disabled></textarea>`;
            }

            questionHTML += `
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-question" data-question-id="new_question_${questionCount}">Remove</button>
                </div>`;

            newQuestionsDiv.insertAdjacentHTML('beforeend', questionHTML);
            addRemoveQuestionListeners();
        }

        function addRemoveQuestionListeners() {
            document.querySelectorAll('.remove-question').forEach(function(button) {
                button.addEventListener('click', function() {
                    const questionId = button.getAttribute('data-question-id');
                    document.getElementById(questionId).remove();
                });
            });

            document.querySelectorAll('.remove-existing-question').forEach(function(button) {
                button.addEventListener('click', function() {
                    const questionId = button.getAttribute('data-question-id');
                    const deleteField = document.getElementById('delete-questions');
                    let deleteValues = deleteField.value ? deleteField.value.split(',') : [];
                    deleteValues.push(questionId);
                    deleteField.value = deleteValues.join(',');

                    // Hide the question from the view
                    document.getElementById(`existing_question_${questionId}`).remove();
                });
            });
        }

        addRemoveQuestionListeners();
    </script>
@endsection
