<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Response;
use App\Models\User;
use App\Models\AcademicYear;
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

        // Check if an academic year is selected; otherwise, default to current academic year
        $selectedYearId = $request->input('academic_year_id', AcademicYear::where('is_current', true)->first()->id);

        // Filter evaluations by selected academic year
        $evaluations = Evaluation::where('academic_year_id', $selectedYearId)
            ->with('creator')
            ->get();

        return view('evaluations.index', compact('evaluations', 'academicYears', 'selectedYearId'));
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

    public function storeResponse(Request $request, $evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);

        // Loop through each response from the form
        foreach ($request->responses as $questionId => $response) {
            Response::create([
                'evaluation_id' => $evaluationId,
                'question_id' => $questionId,
                'user_id' => auth()->id(),
                'response_text' => $response['response_text'] ?? null,
                'response_value' => $response['response_value'] ?? null
            ]);
        }

        return redirect()->route('evaluations.index')->with('success', 'Responses saved successfully.');
    }

    public function showResponseForm($evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $questions = $evaluation->questions;

        return view('evaluations.submit_response', compact('evaluation', 'questions'));
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
    
        // Determine recipients based on role selected, ensuring only active users are considered
        if ($role === 'all') {
            // Query all users with role IDs for Faculty, Company, and Student with active accounts
            $recipients = User::whereIn('role_id', [3, 4, 5])
                              ->where('status_id', $activeStatusId)
                              ->get();
        } else {
            // Fetch users based on the selected role and active status
            $roleIds = [
                'faculty' => 3,
                'company' => 4,
                'student' => 5,
            ];
    
            $recipients = User::where('role_id', $roleIds[$role])
                              ->where('status_id', $activeStatusId)
                              ->get();
        }
    
        // Update the evaluation with the recipient role
        $evaluation->recipient_role = $role;
        $evaluation->save();
    
        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(new EvaluationNotification($evaluation));
        }
    
        return redirect()->route('evaluations.index')->with('success', 'Evaluation sent successfully!');
    }
    

    public function recipientIndex()
    {
        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Get the current user's role
        $userRole = auth()->user()->role->role_name; // Get the role name (like 'company', 'student')

        // Fetch evaluations that have been sent to the current user's role or 'all' roles for the current academic year
        $evaluations = Evaluation::where('academic_year_id', $currentAcademicYear->id)
            ->where(function ($query) use ($userRole) {
                $query->where('recipient_role', $userRole)
                    ->orWhere('recipient_role', 'all'); // Assuming you use 'all' for all roles
            })
            ->get();

        return view('evaluations.recipient_index', compact('evaluations'));
    }

}
