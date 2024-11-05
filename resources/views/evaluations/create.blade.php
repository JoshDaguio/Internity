@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Create New Evaluations</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Evaluations</li>
                <li class="breadcrumb-item active">Create Evaluation</li>
            </ol>
        </nav>
    </div>

    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">New Form</h5>
                    <form action="{{ route('evaluations.store') }}" method="POST">
                        @csrf
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <select id="evaluation_type" name="evaluation_type" class="form-control" required>
                                    <option value="program">Internship Program</option>
                                    <option value="intern_student">Intern Performance Evaluation</option>
                                    <option value="intern_company">Intern Exit Form</option>
                                </select>
                                <label for="evaluation_type">Evaluation Type</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <input type="text" id="title" name="title" class="form-control" placeholder="Evaluation Title" required>
                                <label for="title">Evaluation Title</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <textarea id="description" name="description" class="form-control" placeholder="Description" style="height: 150px;" required></textarea>
                                <label for="description">Description</label>
                            </div>
                        </div>

                        <h5 class="card-title">Questions</h5>                         
                        <div id="questions">
                            <!-- Dynamic question area -->
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="button" id="addRadioQuestion" class="btn btn-success btn-sm mb-3"><i class="bi bi-plus-circle"></i> Radio Question</button>
                            <button type="button" id="addCommentQuestion" class="btn btn-success btn-sm mb-3 me-3"><i class="bi bi-plus-circle"></i> Text Question</button>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4">Create</button>
                        <a href="{{ route('evaluations.index') }}" class="btn btn-secondary mt-4">Cancel</a>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dynamically add questions based on the button clicked
        document.getElementById('addCommentQuestion').addEventListener('click', function() {
            addQuestion('long_text', 'Text Question');
        });

        document.getElementById('addRadioQuestion').addEventListener('click', function() {
            addQuestion('radio', 'Radio Button');
        });

        function addQuestion(type, label) {
            const questionsDiv = document.getElementById('questions');
            const questionCount = questionsDiv.children.length;

            let questionHTML = `
                <div class="form-group mb-3" id="question_${questionCount}">
                    <label class="mb-2"><strong>Question ${questionCount + 1}</strong></label>
                    <input type="text" name="questions[${questionCount}][text]" class="form-control mb-2" required placeholder="Enter question text">
                    <input type="hidden" name="questions[${questionCount}][type]" value="${type}">
                    <label>${label}</label>`;

            if (type === 'radio') {
                questionHTML += `
                    <div class="d-flex justify-content-center mt-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="questions[${questionCount}][value]" id="radio1_${questionCount}" value="1" disabled>
                            <label class="form-check-label" for="radio1_${questionCount}">1</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="questions[${questionCount}][value]" id="radio2_${questionCount}" value="2" disabled>
                            <label class="form-check-label" for="radio2_${questionCount}">2</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="questions[${questionCount}][value]" id="radio3_${questionCount}" value="3" disabled>
                            <label class="form-check-label" for="radio3_${questionCount}">3</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="questions[${questionCount}][value]" id="radio4_${questionCount}" value="4" disabled>
                            <label class="form-check-label" for="radio4_${questionCount}">4</label>
                        </div>
                    </div>`;
            } else if (type === 'long_text') {
                questionHTML += `
                    <textarea class="form-control" rows="3" placeholder="Text Question" disabled></textarea>`;
            }

            questionHTML += `
                <button type="button" class="btn btn-danger btn-sm mt-2 remove-question" data-question="question_${questionCount}">Remove</button>
                </div>`;

            questionsDiv.insertAdjacentHTML('beforeend', questionHTML);
            addRemoveQuestionListeners();
        }

        // Function to add the remove button listener
        function addRemoveQuestionListeners() {
            document.querySelectorAll('.remove-question').forEach(function(button) {
                button.addEventListener('click', function() {
                    const questionId = button.getAttribute('data-question');
                    document.getElementById(questionId).remove();
                });
            });
        }

        // Initial call to attach listeners
        addRemoveQuestionListeners();
    </script>
@endsection
