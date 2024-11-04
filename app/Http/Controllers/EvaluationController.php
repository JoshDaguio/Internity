<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Response;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\AcceptedInternship;
use App\Models\DailyTimeRecord;
use App\Models\EvaluationRecipient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;
use App\Exports\EvaluationResultsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail; 
use App\Mail\EvaluationNotification; 

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
         // Get all academic years for filter dropdown
        $academicYears = AcademicYear::all();
        $evaluationTypes = ['program', 'intern_student', 'intern_company']; // Define evaluation types

        // Check if an academic year is selected; otherwise, default to current academic year
        $selectedYearId = $request->input('academic_year_id', AcademicYear::where('is_current', true)->first()->id);

        // Get selected evaluation type, default to all
        $selectedEvaluationType = $request->input('evaluation_type', '');

        // Filter evaluations by selected academic year and evaluation type if provided
        $evaluations = Evaluation::where('academic_year_id', $selectedYearId)
            ->when($selectedEvaluationType, function ($query) use ($selectedEvaluationType) {
                $query->where('evaluation_type', $selectedEvaluationType);
            })
            ->with('creator')
            ->get();


        return view('evaluations.index', compact('evaluations', 'academicYears', 'selectedYearId', 'evaluationTypes', 'selectedEvaluationType'));
    }

    public function create()
    {
        return view('evaluations.create');
    }

    public function store(Request $request)
    {
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $evaluation = Evaluation::create([
            'title' => $request->title,
            'description' => $request->description,
            'evaluation_type' => $request->evaluation_type,
            'created_by' => auth()->id(),
            'academic_year_id' => $currentAcademicYear->id, // Add the current academic year
        ]);

        foreach ($request->questions as $question) {
            Question::create([
                'evaluation_id' => $evaluation->id,
                'question_text' => $question['text'],
                'question_type' => $question['type']
            ]);
        }

        return redirect()->route('evaluations.index')->with('success', 'Evaluation created successfully');
    }

    public function manageQuestions($evaluationId)
    {
        $evaluation = Evaluation::with('questions')->findOrFail($evaluationId);

        return view('evaluations.manage_questions', compact('evaluation'));
    }

    public function updateQuestions(Request $request, $evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);

        // Update existing questions
        if ($request->has('questions')) {
            foreach ($request->questions as $questionId => $questionData) {
                $question = Question::find($questionId);
                if ($question) {
                    $question->update([
                        'question_text' => $questionData['text'],
                        'question_type' => $questionData['type']
                    ]);
                }
            }
        }

        // Add new questions
        if ($request->has('new_questions')) {
            foreach ($request->new_questions as $newQuestion) {
                if (!empty($newQuestion['text']) && !empty($newQuestion['type'])) {
                    Question::create([
                        'evaluation_id' => $evaluation->id,
                        'question_text' => $newQuestion['text'],
                        'question_type' => $newQuestion['type']
                    ]);
                }
            }
        }

        // Delete questions if requested
        if (!empty($request->delete_questions)) {
            $deleteQuestionIds = explode(',', $request->delete_questions);
            Question::whereIn('id', $deleteQuestionIds)->delete();
        }

        return redirect()->route('evaluations.manageQuestions', $evaluationId)->with('success', 'Questions updated successfully');
    }



    public function storeResponse(Request $request, $evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $evaluatorId = auth()->id();

        // Check if the recipient has already answered this evaluation
        $recipientRecord = EvaluationRecipient::where('evaluation_id', $evaluationId)
            ->where('user_id', $evaluatorId)
            ->where('evaluatee_id', $request->input('evaluatee_id')) // Check for evaluatee in `intern_student` evaluations
            ->first();

        if ($recipientRecord && $recipientRecord->is_answered) {
            // If already answered, redirect with a message
            return redirect()->route('evaluations.recipientIndex')->with('error', 'You have already completed this evaluation.');
        }

        // Validate supervisor_name only if needed
        if ($evaluation->evaluation_type === 'intern_company') {
            $request->validate([
                'supervisor_name' => 'nullable|string|max:255',
            ]);
        }

        // Fetch the supervisor name directly from the form input
        // $supervisorName = $request->input('supervisor_name');
    
        // Check evaluation type and handle response saving accordingly
        if ($evaluation->evaluation_type === 'program') {
            // Save responses for 'program' type evaluations
            foreach ($request->responses as $questionId => $response) {
                // Skip if this is not a question ID (like 'supervisor_name')
                if (!is_numeric($questionId)) {
                    continue;
                }
    
                Response::create([
                    'evaluation_id' => $evaluationId,
                    'question_id' => $questionId,
                    'evaluator' => $evaluatorId,
                    'response_text' => $response['response_text'] ?? null,
                    'response_value' => $response['response_value'] ?? null
                ]);
            }
        } elseif ($evaluation->evaluation_type === 'intern_company') {
            // Retrieve the student's accepted internship to get the company being evaluated
            $acceptedInternship = AcceptedInternship::where('student_id', $evaluatorId)->first();
            $companyId = $acceptedInternship ? $acceptedInternship->company_id : null;

            $supervisorName = $request->input('supervisor_name');

            // Save responses for each question and include extra details (total score, supervisor name)
            foreach ($request->responses as $questionId => $response) {
                // Skip if this is not a question ID (like 'supervisor_name')
                if (!is_numeric($questionId)) {
                    continue;
                }

                Response::create([
                    'evaluation_id' => $evaluationId,
                    'question_id' => $questionId,
                    'evaluator' => $evaluatorId,
                    'evaluatee' => $companyId, // Save company ID as evaluatee
                    'response_text' => $response['response_text'] ?? null,
                    'response_value' => $response['response_value'] ?? null,
                    'supervisor' => $supervisorName,
                ]);
            }
        } elseif ($evaluation->evaluation_type === 'intern_student') {
            $evaluateeId = $request->input('evaluatee_id'); // The student being evaluated
    
            foreach ($request->responses as $questionId => $response) {
                if (!is_numeric($questionId)) continue;
    
                Response::create([
                    'evaluation_id' => $evaluationId,
                    'question_id' => $questionId,
                    'evaluator' => $evaluatorId,
                    'evaluatee' => $evaluateeId,
                    'response_text' => $response['response_text'] ?? null,
                    'response_value' => $response['response_value'] ?? null
                ]);
            }
        }
    
        // Update the 'is_answered' field for any evaluation
        EvaluationRecipient::where('evaluation_id', $evaluationId)
                            ->where('user_id', auth()->id())
                            ->where('evaluatee_id', $request->input('evaluatee_id')) // Ensure only the relevant evaluation is updated
                            ->update(['is_answered' => true]);
    

        // Redirect based on user role
        $userRole = auth()->user()->role_id; // Replace with role identifier field, e.g., 'role_id' or 'role->name'
        if (in_array($userRole, [1, 2])) { // Assuming 1 is Super Admin, 2 is Admin
            return redirect()->route('evaluations.index')->with('success', 'Responses saved successfully.');
        } else { // Assuming other roles (e.g., company, student, faculty)
            return redirect()->route('evaluations.recipientIndex')->with('success', 'Responses saved successfully.');
        }
    }
    

    public function showResponseForm($evaluationId)
    {
        $user = auth()->user();

        $evaluation = Evaluation::findOrFail($evaluationId);

        // Check if already answered
        $recipientRecord = EvaluationRecipient::where('evaluation_id', $evaluationId)
                                            ->where('user_id', $user->id)
                                            ->first();

        if ($recipientRecord && $recipientRecord->is_answered) {
            return redirect()->route('evaluations.recipientIndex')->with('error', 'You have already completed this evaluation.');
        }

        // Retrieve the evaluatee (intern) being evaluated
        $evaluatee = User::find($recipientRecord->evaluatee_id);
            
        $questions = $evaluation->questions;
        return view('evaluations.submit_response', compact('evaluation', 'questions', 'user', 'evaluatee'));
    }

    public function viewResults($evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $results = $this->calculateResults($evaluation);
    
        return view('evaluations.results', compact('evaluation', 'results'));
    }
    
    public function calculateResults($evaluation)
    {
        $questions = $evaluation->questions;
        $results = [];

        foreach ($questions as $question) {
            if ($question->question_type === 'radio') {
                $totalResponses = $question->responses->count();
                $sum = $question->responses->sum('response_value');
                $average = $totalResponses > 0 ? $sum / $totalResponses : 0;

                // Calculate number of responses for each option (1-4)
                $responseCounts = [
                    '1' => $question->responses->where('response_value', 1)->count(),
                    '2' => $question->responses->where('response_value', 2)->count(),
                    '3' => $question->responses->where('response_value', 3)->count(),
                    '4' => $question->responses->where('response_value', 4)->count(),
                ];

                // Calculate percentages for each response option
                $percentages = [];
                foreach ($responseCounts as $rating => $count) {
                    $percentages[$rating] = $totalResponses > 0 ? round(($count / $totalResponses) * 100, 2) : 0;
                }

                // Weighted mean calculation (total points divided by number of responses)
                $weightedSum = 0;
                foreach ($responseCounts as $rating => $count) {
                    $weightedSum += $rating * $count;
                }
                $weightedMean = $totalResponses > 0 ? round($weightedSum / $totalResponses, 2) : 0;

                // Determine remarks based on the weighted mean
                $remarks = '';
                if ($weightedMean >= 4.5) {
                    $remarks = 'Strongly Agree';
                } elseif ($weightedMean >= 3.5) {
                    $remarks = 'Agree';
                } elseif ($weightedMean >= 2.5) {
                    $remarks = 'Neutral';
                } elseif ($weightedMean >= 1.5) {
                    $remarks = 'Disagree';
                } else {
                    $remarks = 'Strongly Disagree';
                }

                // Populate result array including percentages and average
                $results[$question->id] = [
                    'responseCounts' => $responseCounts,
                    'percentages' => $percentages,  // Include percentage here
                    'totalResponses' => $totalResponses,
                    'average' => $average, // Include average here
                    'weightedMean' => $weightedMean,
                    'remarks' => $remarks
                ];
            } elseif ($question->question_type === 'long_text') {
                // Collect long text responses
                $results[$question->id]['responses'] = $question->responses->pluck('response_text');
            }
        }

        return $results;
    }

    
    
    public function downloadPDF($evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $results = $this->calculateResults($evaluation);
    
        $dompdf = new Dompdf();
        $pdfView = View::make('evaluations.pdf', compact('evaluation', 'results'))->render();
    
        $dompdf->loadHtml($pdfView);
        $dompdf->render();
    
        return $dompdf->stream('evaluation-results.pdf');
    }
    
 
    public function downloadExcel($evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        return Excel::download(new EvaluationResultsExport($evaluation), 'evaluation-results.xlsx');
    }

    public function sendEvaluation(Request $request, $evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $role = $request->recipient_role;
    
        // Fetch the active status ID (assuming 1 is 'Active')
        $activeStatusId = 1;
    
        // Specific Student ID Logic
        if ($request->has('student_id')) {
            $recipients = User::where('id', $request->student_id)
                            ->where('status_id', $activeStatusId)
                            ->get();
        } else {
            // Other logic as previously implemented
            if ($role === 'all') {
                $recipients = User::whereIn('role_id', [3, 4, 5])
                                ->where('status_id', $activeStatusId)
                                ->get();
            } else {
                $roleIds = [
                    'faculty' => 3,
                    'company' => 4,
                    'student' => 5,
                ];
                $recipients = User::where('role_id', $roleIds[$role])
                                ->where('status_id', $activeStatusId)
                                ->get();
            }
        }
        // Update the evaluation with the recipient role
        $evaluation->recipient_role = $role;
        $evaluation->save();
    
        foreach ($recipients as $recipient) {
            // Send email notification
            Mail::to($recipient->email)->send(new EvaluationNotification($evaluation));
    
            // Use the EvaluationRecipient model to record sending
            EvaluationRecipient::updateOrCreate([
                'evaluation_id' => $evaluationId,
                'user_id' => $recipient->id,
            ], [
                'is_answered' => false,
            ]);
        }
    
        return redirect()->route('evaluations.index')->with('success', 'Evaluation sent successfully!');
    }
    

    public function recipientIndex()
    {
        $user = auth()->user();

        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get the current user's role
        $userRole = auth()->user()->role->role_name; // Get the role name (like 'company', 'student')

        // Fetch evaluations that have been sent to the user based on role or specifically to the user
        $evaluations = Evaluation::where('academic_year_id', $currentAcademicYear->id)
            ->whereHas('recipients', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('creator')
            ->get();

        foreach ($evaluations as $evaluation) {
            $recipient = EvaluationRecipient::where('evaluation_id', $evaluation->id)
                                            ->where('user_id', $user->id)
                                            ->first();

            $evaluation->is_answered = $recipient ? $recipient->is_answered : false;
        }

        // Get the first completed intern_student evaluation for the student
        $completedCompanyEvaluation = Evaluation::where('evaluation_type', 'intern_student')
            ->whereHas('recipients', function ($query) use ($user) {
                $query->where('evaluatee_id', $user->id)
                    ->where('is_answered', true);
            })
            ->first();

        return view('evaluations.recipient_index', compact('evaluations', 'completedCompanyEvaluation'));
    }

    public function viewUserResponse($evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $responses = [];
        $totalScore = null;
        $totalPossibleScore = null;
        $evaluationResult = null;
        $evaluatee = null; 
        $user = auth()->user();
    
        if ($evaluation->evaluation_type === 'program') {
            // Program evaluations show detailed responses
            $responses = Response::where('evaluation_id', $evaluationId)
                                ->where('evaluator', auth()->id())
                                ->with('question')
                                ->get();
    
            list($totalScore, $totalPossibleScore) = $this->calculateScore($evaluationId);
        } elseif ($evaluation->evaluation_type === 'intern_company'|| $evaluation->evaluation_type === 'intern_student') {
            // Fetch responses for 'intern_company' evaluations
            $responses = Response::where('evaluation_id', $evaluationId)
                                ->where('evaluator', auth()->id())
                                ->with('question')
                                ->get();
    
            // Retrieve one of the responses to get the supervisor name
            $evaluationResult = $responses->first();
            if ($evaluation->evaluation_type === 'intern_student') {
                $recipientRecord = EvaluationRecipient::where('evaluation_id', $evaluationId)
                                                     ->where('user_id', auth()->id())
                                                     ->first();
                $evaluatee = User::find($recipientRecord->evaluatee_id); // Get the evaluated intern
            }
            
            list($totalScore, $totalPossibleScore) = $this->calculateScore($evaluationId);
        }
    
        return view('evaluations.view_response', compact('evaluation', 'responses', 'totalScore', 'totalPossibleScore', 'evaluationResult', 'evaluatee', 'user'));
    }
    
    

    public function downloadResponsePDF($evaluationId)
    {
        $user = auth()->user();
        $evaluation = Evaluation::findOrFail($evaluationId);
        $responses = [];
        $totalScore = null;
        $totalPossibleScore = null;
        $evaluationResult = null;
        $evaluatee = null;
    
        // Fetch responses and details
        if ($evaluation->evaluation_type === 'program') {
            $responses = Response::where('evaluation_id', $evaluationId)
                                ->where('evaluator', $user->id)
                                ->with('question')
                                ->get();
            list($totalScore, $totalPossibleScore) = $this->calculateScore($evaluationId);
        } elseif ($evaluation->evaluation_type === 'intern_company' || $evaluation->evaluation_type === 'intern_student') {
            $responses = Response::where('evaluation_id', $evaluationId)
                                ->where('evaluator', $user->id)
                                ->with('question')
                                ->get();
            // Fetch the supervisor name from the first response if present
            $evaluationResult = $responses->first();
            if ($evaluation->evaluation_type === 'intern_student') {
                $recipientRecord = EvaluationRecipient::where('evaluation_id', $evaluationId)
                                                     ->where('user_id', auth()->id())
                                                     ->first();
                $evaluatee = User::find($recipientRecord->evaluatee_id); // Get the evaluated intern
            }
            list($totalScore, $totalPossibleScore) = $this->calculateScore($evaluationId);
        }
    
        // Generate PDF using Dompdf
        $dompdf = new Dompdf();
        $pdfView = View::make('evaluations.response_pdf', compact('evaluation', 'responses', 'user', 'totalScore', 'totalPossibleScore', 'evaluationResult', 'evaluatee'))->render();
        $dompdf->loadHtml($pdfView);
        $dompdf->render();
    
        return $dompdf->stream('evaluation-response.pdf');
    }
    


    public function internCompanyRecipientList(Request $request, $evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        
        // Fetch all students with their accepted internships
        $students = User::where('role_id', 5) // Assuming role_id 5 is for students
                        ->whereHas('acceptedInternship') // Ensure they have an accepted internship
                        ->get();

        $studentsWithCompleteInternship = [];

        foreach ($students as $student) {
            $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();

            if ($acceptedInternship) {
                $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
                                                    ->orderBy('log_date', 'desc')
                                                    ->first();

                if ($latestDailyRecord && $latestDailyRecord->remaining_hours == 0) {
                    $student->company = User::find($acceptedInternship->company_id);
                    $studentsWithCompleteInternship[] = $student;
                }
            }
        }

        // Use the new model to fetch received and answered status
        $receivedIds = EvaluationRecipient::where('evaluation_id', $evaluationId)
                                        ->pluck('user_id')
                                        ->toArray();

        $answeredIds = EvaluationRecipient::where('evaluation_id', $evaluationId)
                                        ->where('is_answered', true)
                                        ->pluck('user_id')
                                        ->toArray();

        // Handle filter based on the request parameter
        $filter = $request->input('filter', 'all');
        if ($filter === 'received') {
            $studentsWithCompleteInternship = array_filter($studentsWithCompleteInternship, function($student) use ($receivedIds) {
                return in_array($student->id, $receivedIds);
            });
        } elseif ($filter === 'not_received') {
            $studentsWithCompleteInternship = array_filter($studentsWithCompleteInternship, function($student) use ($receivedIds) {
                return !in_array($student->id, $receivedIds);
            });
        } elseif ($filter === 'answered') {
            $studentsWithCompleteInternship = array_filter($studentsWithCompleteInternship, function($student) use ($answeredIds) {
                return in_array($student->id, $answeredIds);
            });
        } elseif ($filter === 'not_answered') {
            $studentsWithCompleteInternship = array_filter($studentsWithCompleteInternship, function($student) use ($answeredIds) {
                return !in_array($student->id, $answeredIds);
            });
        }

        return view('evaluations.intern_company_recipients', compact('evaluation', 'studentsWithCompleteInternship', 'receivedIds', 'answeredIds', 'filter'));
    }

    

    private function calculateScore($evaluationId, $evaluateeId = null)
    {
        $questions = Question::where('evaluation_id', $evaluationId)
                             ->where('question_type', 'radio')
                             ->get();
    
                             $totalScoreQuery = Response::where('evaluation_id', $evaluationId)
                             ->whereNotNull('response_value');

        $totalPossibleScore = $questions->count() * 4;

        // Add conditional check for evaluatee or evaluator
        if ($evaluateeId) {
            $totalScoreQuery->where('evaluatee', $evaluateeId);
        } else {
            $totalScoreQuery->where('evaluator', auth()->id());
        }

        $totalScore = $totalScoreQuery->sum('response_value');
    
        return [$totalScore, $totalPossibleScore];
    }


    public function sendInternStudentEvaluation(Request $request, $evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
    
        // Get companies with interns who have completed their internship
        $companiesWithInterns = User::where('role_id', 4)
            ->with(['interns' => function ($query) {
                $query->whereHas('student.dailyTimeRecords', function ($subQuery) {
                    $subQuery->where('remaining_hours', 0);
                });
            }])
            ->get();
    
        foreach ($companiesWithInterns as $company) {
            foreach ($company->interns as $intern) {
                $studentId = $intern->student_id;
                $exists = EvaluationRecipient::where('evaluation_id', $evaluationId)
                    ->where('user_id', $company->id)
                    ->where('evaluatee_id', $studentId)
                    ->exists();
    
                if (!$exists) {
                    Mail::to($company->email)->send(new EvaluationNotification($evaluation));
    
                    EvaluationRecipient::create([
                        'evaluation_id' => $evaluationId,
                        'user_id' => $company->id,
                        'evaluatee_id' => $studentId,
                        'is_answered' => false,
                    ]);
                }
            }
        }
    
        return redirect()->route('evaluations.index')->with('success', 'Evaluation sent to companies with eligible interns.');
    }
    

    public function internStudentRecipientList(Request $request, $evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);

        // Fetch companies with interns who have completed their internship hours
        $companiesWithInterns = User::where('role_id', 4) // Assuming role_id 4 is for companies
            ->with(['interns.student.profile', 'interns' => function ($query) {
                $query->whereHas('student.dailyTimeRecords', function ($subQuery) {
                    $subQuery->where('remaining_hours', 0);
                });
            }])
            ->get();

        // Filter by sent status: all, received, not received, answered, not answered
        $receivedIds = EvaluationRecipient::where('evaluation_id', $evaluationId)
                                        ->pluck('evaluatee_id')
                                        ->toArray();
        $answeredIds = EvaluationRecipient::where('evaluation_id', $evaluationId)
                                        ->where('is_answered', true)
                                        ->pluck('evaluatee_id')
                                        ->toArray();

        $filter = $request->input('filter', 'all');
        foreach ($companiesWithInterns as $company) {
            $company->interns = $company->interns->filter(function ($intern) use ($filter, $receivedIds, $answeredIds) {
                $studentId = $intern->student_id;
                if ($filter === 'received') {
                    return in_array($studentId, $receivedIds);
                } elseif ($filter === 'not_received') {
                    return !in_array($studentId, $receivedIds);
                } elseif ($filter === 'answered') {
                    return in_array($studentId, $answeredIds);
                } elseif ($filter === 'not_answered') {
                    return !in_array($studentId, $answeredIds);
                }
                return true;
            });
        }

        return view('evaluations.intern_student_recipients', compact('evaluation', 'companiesWithInterns', 'receivedIds', 'answeredIds', 'filter'));
    }

    // Student view of the Intern Evaluation by Company
    public function viewStudentEvaluation($evaluationId, $studentId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $student = User::findOrFail($studentId);

        // Retrieve student-specific responses
        $responses = Response::where('evaluation_id', $evaluationId)
                            ->where('evaluatee', $studentId)
                            ->with('question')
                            ->get();

        list($totalScore, $totalPossibleScore) = $this->calculateScore($evaluationId, $studentId);
        

        return view('evaluations.student_view_response', compact('evaluation', 'responses', 'totalScore', 'totalPossibleScore', 'student'));
    }

    public function downloadStudentEvaluationPDF($evaluationId, $studentId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $student = User::findOrFail($studentId);

        // Fetch responses for PDF generation
        $responses = Response::where('evaluation_id', $evaluationId)
                            ->where('evaluatee', $studentId)
                            ->with('question')
                            ->get();

        list($totalScore, $totalPossibleScore) = $this->calculateScore($evaluationId, $studentId);

        // Generate PDF using Dompdf
        $dompdf = new Dompdf();
        $pdfView = View::make('evaluations.student_evaluation_pdf', compact('evaluation', 'responses', 'student', 'totalScore', 'totalPossibleScore'))->render();
        $dompdf->loadHtml($pdfView);
        $dompdf->render();

        return $dompdf->stream('student-evaluation-response.pdf');
    }


    public function viewStudentScores($evaluationId, $studentId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $student = User::findOrFail($studentId);

        // Retrieve student-specific responses
        $responses = Response::where('evaluation_id', $evaluationId)
                            ->where('evaluatee', $studentId)
                            ->with('question')
                            ->get();

        list($totalScore, $totalPossibleScore) = $this->calculateScore($evaluationId, $studentId);

        // Fetch the associated company for the student's internship
        $company = $student->acceptedInternship->company ?? null;

        return view('evaluations.admin_view_student_scores', compact('evaluation', 'responses', 'totalScore', 'totalPossibleScore', 'student', 'company'));
    }

    public function downloadStudentScoresPDF($evaluationId, $studentId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $student = User::findOrFail($studentId);

        // Fetch responses for PDF generation
        $responses = Response::where('evaluation_id', $evaluationId)
                            ->where('evaluatee', $studentId)
                            ->with('question')
                            ->get();

        list($totalScore, $totalPossibleScore) = $this->calculateScore($evaluationId, $studentId);

        // Fetch the associated company for the student's internship
        $company = $student->acceptedInternship->company ?? null;

        // Generate PDF using Dompdf
        $dompdf = new Dompdf();
        $pdfView = View::make('evaluations.admin_student_scores_pdf', compact('evaluation', 'responses', 'student', 'totalScore', 'totalPossibleScore', 'company'))->render();
        $dompdf->loadHtml($pdfView);
        $dompdf->render();

        return $dompdf->stream('student-scores.pdf');
    }



}
