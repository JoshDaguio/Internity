<?php

namespace App\Exports;

use App\Models\Evaluation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluationResultsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $evaluation;

    public function __construct(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation;
    }

    public function collection()
    {
        return $this->evaluation->questions;
    }

    public function headings(): array
    {
        return [
            'Question',
            'Response Counts (4, 3, 2, 1)',
            'Percentages (%)',
            'Population (Total Responses)',
            'Average Score',
            'Weighted Mean',
            'Remarks',
            'Text Response'
        ];
    }

    public function map($question): array
    {
        $results = $this->calculateResults($this->evaluation);
        
        if ($question->question_type === 'radio') {
            return [
                $question->question_text,
                '4: ' . ($results[$question->id]['responseCounts'][4] ?? 0) . ' | 3: ' . ($results[$question->id]['responseCounts'][3] ?? 0) . ' | 2: ' . ($results[$question->id]['responseCounts'][2] ?? 0) . ' | 1: ' . ($results[$question->id]['responseCounts'][1] ?? 0),
                '4: ' . ($results[$question->id]['percentages'][4] ?? 0) . '% | 3: ' . ($results[$question->id]['percentages'][3] ?? 0) . '% | 2: ' . ($results[$question->id]['percentages'][2] ?? 0) . '% | 1: ' . ($results[$question->id]['percentages'][1] ?? 0) . '%',
                $results[$question->id]['totalResponses'],
                round($results[$question->id]['average'], 2),
                $results[$question->id]['weightedMean'],
                $results[$question->id]['remarks'],
                'N/A'
            ];
        } elseif ($question->question_type === 'long_text') {
            // Return each text response on a new row
            $mappedResponses = [];
            foreach ($results[$question->id]['responses'] as $response) {
                $mappedResponses[] = [
                    $question->question_text,
                    'N/A',
                    'N/A',
                    'N/A',
                    'N/A',
                    'N/A',
                    'N/A',
                    $response
                ];
            }
            return $mappedResponses;
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
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

                $responseCounts = [
                    '1' => $question->responses->where('response_value', 1)->count(),
                    '2' => $question->responses->where('response_value', 2)->count(),
                    '3' => $question->responses->where('response_value', 3)->count(),
                    '4' => $question->responses->where('response_value', 4)->count(),
                ];

                $percentages = [];
                foreach ($responseCounts as $rating => $count) {
                    $percentages[$rating] = $totalResponses > 0 ? round(($count / $totalResponses) * 100, 2) : 0;
                }

                $weightedSum = 0;
                foreach ($responseCounts as $rating => $count) {
                    $weightedSum += $rating * $count;
                }
                $weightedMean = $totalResponses > 0 ? round($weightedSum / $totalResponses, 2) : 0;

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

                $results[$question->id] = [
                    'responseCounts' => $responseCounts,
                    'percentages' => $percentages,
                    'totalResponses' => $totalResponses,
                    'average' => $average,
                    'weightedMean' => $weightedMean,
                    'remarks' => $remarks,
                ];
            } elseif ($question->question_type === 'long_text') {
                $results[$question->id]['responses'] = $question->responses->pluck('response_text');
            }
        }

        return $results;
    }
}
