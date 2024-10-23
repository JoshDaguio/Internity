<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\Question;
use App\Models\Response;
use App\Model\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::with('creator')->get();
        return view('evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        return view('evaluations.create');
    }

    public function store(Request $request)
    {
        $evaluation = Evaluation::create([
            'title' => $request->title,
            'description' => $request->description,
            'evaluation_type' => $request->evaluation_type,
            'created_by' => auth()->id()
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

    public function viewResults($evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $results = $this->calculateResults($evaluation);
    
        return view('evaluations.results', compact('evaluation', 'results'));
    }
    

    public function showResponseForm($evaluationId)
    {
        $evaluation = Evaluation::findOrFail($evaluationId);
        $questions = $evaluation->questions;

        return view('evaluations.submit_response', compact('evaluation', 'questions'));
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
    
 

}
