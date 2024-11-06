<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Requirement;
use App\Models\User;
use App\Models\CompletionRequirement;
use App\Models\MonthlyReport;
use App\Models\AcceptedInternship;
use App\Models\DailyTimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Carbon\Carbon;


class InternshipFilesController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $requirements = Requirement::where('student_id', $student->id)->first();
        $completionReqs = CompletionRequirement::where('student_id', $student->id)->first();
        $profile = $student->profile;

        // Check if the student has an accepted internship
        $acceptedInternship = $student->acceptedInternship()->first();
        $hasAcceptedInternship = $acceptedInternship !== null;
        $startDate = $hasAcceptedInternship ? Carbon::parse($acceptedInternship->start_date) : null;

        // Calculate the estimated finish date
        if ($hasAcceptedInternship) {
            // Fetch internship hours based on course
            $internshipHours = $student->course->internshipHours->hours ?? 0;

            // Fetch all Daily Time Records for this student
            $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();
            
            // Fetch the latest Daily Time Record for this student (even if it's not today)
            $latestDailyRecord = $dailyRecords->sortByDesc('log_date')->first();
            
            // Calculate total worked hours
            $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
            
            // Determine remaining hours using latest DTR or fallback
            $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours - $totalWorkedHours;

            // Fetch scheduled days and calculate estimated finish date if needed
            $scheduledDays = $this->getScheduledDays($acceptedInternship, $student);
            $estimatedFinishDate = $remainingHours > 0 
                ? $this->calculateFinishDate($remainingHours, $startDate, $scheduledDays)
                : Carbon::now();
        } else {
            $estimatedFinishDate = null;
        }

        // Check files from requirements and profile
        $files = [
            'endorsement_letter' => $requirements ? $requirements->endorsement_letter : null,
            'waiver' => ($requirements && $requirements->waiver_status_id == 2) ? $requirements->waiver_form : null,
            'medical_certificate' => ($requirements && $requirements->medical_status_id == 2) ? $requirements->medical_certificate : null,
            'resume' => $profile ? $profile->cv_file_path : null,
            'intern_evaluation' => $completionReqs->intern_evaluation ?? null,
            'exit_form' => $completionReqs->exit_form ?? null,
            'certificate_completion' => $completionReqs->certificate_completion ?? null,
        ];

        // Fetch monthly reports for DTR and EOD
        $eodReports = $student->monthlyReports()->where('type', 'eod')->get();
        $dtrReports = $student->monthlyReports()->where('type', 'dtr')->get();

        // Determine if any files are available for download
        $hasFiles = collect($files)->contains(function ($file) {
            return $file !== null;
        }) || $eodReports->isNotEmpty() || $dtrReports->isNotEmpty();

        return view('student.internship_files', compact(
            'files', 'hasAcceptedInternship', 'startDate', 'estimatedFinishDate', 'eodReports', 'dtrReports', 'student', 'requirements', 'completionReqs', 'hasFiles'
        ));
    }

    // Admin and Faculty Views of Student's Internship Files
    public function viewStudentFiles($studentId)
    {
        $student = User::findOrFail($studentId);
        $requirements = Requirement::where('student_id', $student->id)->first();
        $completionReqs = CompletionRequirement::where('student_id', $student->id)->first();
        $profile = $student->profile;

        // Check if the student has an accepted internship
        $acceptedInternship = $student->acceptedInternship()->first();
        $hasAcceptedInternship = $acceptedInternship !== null;
        $startDate = $hasAcceptedInternship ? Carbon::parse($acceptedInternship->start_date) : null;

        // Calculate the estimated finish date
        if ($hasAcceptedInternship) {
            // Fetch internship hours based on course
            $internshipHours = $student->course->internshipHours->hours ?? 0;

            // Fetch all Daily Time Records for this student
            $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();
            
            // Fetch the latest Daily Time Record for this student (even if it's not today)
            $latestDailyRecord = $dailyRecords->sortByDesc('log_date')->first();
            
            // Calculate total worked hours
            $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
            
            // Determine remaining hours using latest DTR or fallback
            $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours - $totalWorkedHours;

            // Fetch scheduled days and calculate estimated finish date if needed
            $scheduledDays = $this->getScheduledDays($acceptedInternship, $student);
            $estimatedFinishDate = $remainingHours > 0 
                ? $this->calculateFinishDate($remainingHours, $startDate, $scheduledDays)
                : Carbon::now();
        } else {
            $estimatedFinishDate = null;
        }

        // Check files from requirements and profile
        $files = [
            'endorsement_letter' => $requirements ? $requirements->endorsement_letter : null,
            'waiver' => ($requirements && $requirements->waiver_status_id == 2) ? $requirements->waiver_form : null,
            'medical_certificate' => ($requirements && $requirements->medical_status_id == 2) ? $requirements->medical_certificate : null,
            'resume' => $profile ? $profile->cv_file_path : null,
            'intern_evaluation' => $completionReqs->intern_evaluation ?? null,
            'exit_form' => $completionReqs->exit_form ?? null,
            'certificate_completion' => $completionReqs->certificate_completion ?? null,
        ];

        // Fetch monthly reports for DTR and EOD
        $eodReports = $student->monthlyReports()->where('type', 'eod')->get();
        $dtrReports = $student->monthlyReports()->where('type', 'dtr')->get();

        return view('administrative.view_student_internship_files', compact(
            'files', 'hasAcceptedInternship', 'startDate', 'estimatedFinishDate', 'eodReports', 'dtrReports', 'student', 'requirements', 'completionReqs'
        ));
    }
    

    public function previewMonthlyReport($type, $id, $studentId = null)
    {
        // Find the Monthly Report by ID, type, and student ID (if provided)
        $query = MonthlyReport::where('id', $id)->where('type', $type);
    
        // If `studentId` is provided (admin side), use it; otherwise, default to the authenticated user (student side)
        if ($studentId) {
            $query->where('student_id', $studentId);
        } else {
            $query->where('student_id', Auth::id());
        }
    
        $report = $query->firstOrFail();
    
        // Fetch and serve the file
        $filePath = storage_path('app/' . $report->file_path);
        $fileMimeType = mime_content_type($filePath);
    
        return response()->file($filePath, [
            'Content-Type' => $fileMimeType,
        ]);
    }
    
    
    public function previewCompletionRequirement($type, $id)
    {
        $completionReq = CompletionRequirement::findOrFail($id);
    
        if ($type == 'intern_evaluation' && $completionReq->intern_evaluation) {
            $filePath = storage_path('app/' . $completionReq->intern_evaluation);
        } elseif ($type == 'exit_form' && $completionReq->exit_form) {
            $filePath = storage_path('app/' . $completionReq->exit_form);
        } elseif ($type == 'certificate_completion' && $completionReq->certificate_completion) {
            $filePath = storage_path('app/' . $completionReq->certificate_completion);
        } else {
            abort(404, 'File not found or access denied.');
        }
    
        $fileMimeType = mime_content_type($filePath);
    
        return response()->file($filePath, [
            'Content-Type' => $fileMimeType,
        ]);
    }
    
    
    // Helper function to determine the file path based on type
    private function getFilePath($student, $type, $id = null)
    {
        if (in_array($type, ['eod', 'dtr']) && $id) {
            $report = MonthlyReport::where('id', $id)
                ->where('type', $type)
                ->where('student_id', $student->id)
                ->first();
            return $report ? $report->file_path : null;
        }
    
        // Files related to other requirement types
        $requirements = Requirement::where('student_id', $student->id)->first();
        $completionReq = CompletionRequirement::where('student_id', $student->id)->first();
        $profile = $student->profile;
    
        return match ($type) {
            'endorsement_letter' => $requirements?->endorsement_letter,
            'waiver' => $requirements?->waiver_status_id == 2 ? $requirements->waiver_form : null,
            'medical_certificate' => $requirements?->medical_status_id == 2 ? $requirements->medical_certificate : null,
            'resume' => $profile?->cv_file_path,
            'intern_evaluation' => $completionReq?->intern_evaluation,
            'exit_form' => $completionReq?->exit_form,
            'certificate_completion' => $completionReq?->certificate_completion,
            default => null,
        };
    }
    
    

    public function uploadCompletionFile(Request $request, $type)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:2048',
        ]);

        $student = Auth::user();
        $completionReq = CompletionRequirement::firstOrCreate(['student_id' => $student->id]);
        $filePath = $request->file('file')->store("completion_files/{$student->id}/$type");

        $completionReq->update([$type => $filePath]);

        return redirect()->back()->with('success', ucfirst(str_replace('_', ' ', $type)) . ' uploaded successfully.');
    }
    
    

    public function downloadAllFiles()
    {
        $student = Auth::user();
        $requirements = Requirement::where('student_id', $student->id)->first();
        $profile = $student->profile;
        $completionRequirements = CompletionRequirement::where('student_id', $student->id)->first(); 
        $eodReports = $student->monthlyReports()->where('type', 'eod')->get();
        $dtrReports = $student->monthlyReports()->where('type', 'dtr')->get();

        $hasFiles = false; // Flag to check if any file exists
        $zip = new ZipArchive;
        $fileName = storage_path('app/public/' . $student->profile->last_name . '_Internship-Files.zip');

        if ($zip->open($fileName, ZipArchive::CREATE) === TRUE) {
            // Adding files to zip

            //Initial Requirements
            if ($requirements->endorsement_letter) {
                $zip->addFile(storage_path('app/' . $requirements->endorsement_letter), 'Endorsement_Letter.pdf');
            }
            if ($requirements->waiver_form && $requirements->waiver_status_id == 2) {
                $zip->addFile(storage_path('app/' . $requirements->waiver_form), 'Waiver_Form.pdf');
            }
            if ($requirements->medical_certificate && $requirements->medical_status_id == 2) {
                $zip->addFile(storage_path('app/' . $requirements->medical_certificate), 'Medical_Certificate.pdf');
            }
            if ($profile->cv_file_path) {
                $zip->addFile(storage_path('app/' . $profile->cv_file_path), 'Resume.pdf');
            }

            //Completion Requirements
            // Adding completion requirements to zip, if available
            if ($completionRequirements) {
                if ($completionRequirements->intern_evaluation) {
                    $zip->addFile(storage_path('app/' . $completionRequirements->intern_evaluation), 'Intern_Evaluation_Form.pdf');
                }
                if ($completionRequirements->exit_form) {
                    $zip->addFile(storage_path('app/' . $completionRequirements->exit_form), 'Intern_Exit_Form.pdf');
                }
                if ($completionRequirements->certificate_completion) {
                    $zip->addFile(storage_path('app/' . $completionRequirements->certificate_completion), 'Certificate_of_Completion.pdf');
                }
            }

            // Monthly Reports
            foreach ($eodReports as $report) {
                $formattedMonthYear = Carbon::parse($report->month_year)->format('Y_m');
                $zip->addFile(storage_path('app/' . $report->file_path), "EOD_Report_{$formattedMonthYear}.pdf");
            }
            foreach ($dtrReports as $report) {
                $formattedMonthYear = Carbon::parse($report->month_year)->format('Y_m');
                $zip->addFile(storage_path('app/' . $report->file_path), "DTR_Report_{$formattedMonthYear}.pdf");
            }
    
            $zip->close();

            $hasFiles = true;
        }

        if ($hasFiles) {
            return response()->download($fileName)->deleteFileAfterSend(true);
        } else {
            // If no files, redirect back with an error message
            return redirect()->back()->with('error', 'No files available for download.');
        }
    }

    private function getScheduledDays($internship, $student)
    {
        if ($student->profile->is_irregular && $internship->custom_schedule) {
            return array_keys($internship->custom_schedule);
        } else {
            $schedule = json_decode($internship->schedule, true);
            return $internship->work_type === 'Hybrid'
                ? array_merge($schedule['onsite_days'], $schedule['remote_days'])
                : $schedule['days'];
        }
    }

    private function calculateRemainingHours($student)
    {
        $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();
        $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
        $internshipHours = $student->course->internshipHours->hours ?? 0;
        return max($internshipHours - $totalWorkedHours, 0);
    }

    private function calculateFinishDate($remainingHours, $startDate, $scheduledDays)
    {
        $estimatedDays = ceil($remainingHours / 8);
        $date = Carbon::now();
        $daysWorked = 0;

        while ($daysWorked < $estimatedDays) {
            if (in_array($date->format('l'), $scheduledDays)) {
                $daysWorked++;
            }
            $date->addDay();
        }

        return $date;
    }


}
