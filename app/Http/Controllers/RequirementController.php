<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Requirement;
use App\Models\RequirementStatus; // Import the status model
use App\Models\User;
use Illuminate\Support\Facades\Storage;


class RequirementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch the student's requirements
        $requirements = Requirement::firstOrCreate(
            ['student_id' => $user->id],
            ['status_id' => 1] // Default status: 'To Review'
        );

        // Pass default values if not set yet
        $waiverStatus = $requirements->waiver_form ? $requirements->status->status : 'Not Submitted';
        $medicalStatus = $requirements->medical_certificate ? $requirements->status->status : 'Not Submitted';
        // $consentStatus = $requirements->consent_form ? $requirements->status->status : 'Not Submitted';

        // Check if Step 1 is completed
        $step1Completed = $requirements->step1Completed();

        return view('requirements.index', compact('requirements', 'waiverStatus', 'medicalStatus', 'step1Completed'));
    }

    // Submit Waiver Form
    public function submitWaiver(Request $request)
    {
        $request->validate([
            'waiver_form' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $student = Auth::user();

        // Store the Waiver Form file
        $waiverFormPath = $request->file('waiver_form')->store('waiver_forms');

        // Update or create the requirement record
        $requirement = Requirement::updateOrCreate(
            ['student_id' => $student->id],
            [
                'waiver_form' => $waiverFormPath,
                'waiver_status_id' => 1, // "To Review"
            ]
        );

        return redirect()->route('requirements.index')->with('success', 'Waiver Form Uploaded.');
    }


    // Submit Medical Certificate
    public function submitMedical(Request $request)
    {
        $request->validate([
            'medical_certificate' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $student = Auth::user();

        // Store the Medical Certificate file
        $medicalCertificatePath = $request->file('medical_certificate')->store('medical_certificates');

        // Update or create the requirement record
        $requirement = Requirement::updateOrCreate(
            ['student_id' => $student->id],
            [
                'medical_certificate' => $medicalCertificatePath,
                'medical_status_id' => 1, // "To Review"
            ]
        );

        return redirect()->route('requirements.index')->with('success', 'Medical Certificate Uploaded.');
    }

    // Method to review a student's requirement
    public function review($requirementId)
    {
        // Find requirements by student_id instead of requirement id
        $requirements = Requirement::with('student')->findOrFail($requirementId);
        
        // Check if Step 1 is completed
        $step1Completed = $requirements->waiver_form && $requirements->medical_certificate && $requirements->status_id == 2;
    
        return view('requirements.review', compact('requirements', 'step1Completed'));
    }

    // File Preview Method
    public function previewFile($type, $id)
    {
        $requirement = Requirement::findOrFail($id);
    
        if ($type == 'waiver' && $requirement->waiver_form) {
            $filePath = storage_path('app/' . $requirement->waiver_form);
        } elseif ($type == 'medical' && $requirement->medical_certificate) {
            $filePath = storage_path('app/' . $requirement->medical_certificate);
        } elseif ($type == 'endorsement' && $requirement->endorsement_letter) {
            $filePath = storage_path('app/' . $requirement->endorsement_letter);
        } else {
            abort(404);
        }
    
    
        $fileMimeType = mime_content_type($filePath);
    
        return response()->file($filePath, [
            'Content-Type' => $fileMimeType,
        ]);
    }

    // Accept Waiver
    public function acceptWaiver($id)
    {
        $requirements = Requirement::findOrFail($id);
        $requirements->waiver_status_id = 2; // Accepted
        $requirements->save();

        // Check if all conditions for completion are met
        $this->checkRequirementCompletion($requirements);
    
        return redirect()->back()->with('success', 'Waiver form accepted.');
    }

    // Reject Waiver
    public function rejectWaiver($id)
    {
        $requirements = Requirement::findOrFail($id);
        $requirements->waiver_status_id = 3; // Rejected
        $requirements->save();

        return redirect()->back()->with('error', 'Waiver form rejected.');
    }

    // Accept Medical Certificate
    public function acceptMedical($id)
    {
        $requirements = Requirement::findOrFail($id);
        $requirements->medical_status_id = 2; // Accepted
        $requirements->save();

        // Check if all conditions for completion are met
        $this->checkRequirementCompletion($requirements);

        return redirect()->back()->with('success', 'Medical certificate accepted.');
    }

    // Reject Medical Certificate
    public function rejectMedical($id)
    {
        $requirements = Requirement::findOrFail($id);
        $requirements->medical_status_id = 3; // Rejected
        $requirements->save();

        return redirect()->back()->with('error', 'Medical certificate rejected.');
    }

    // Upload Endorsement Letter (Step 2)
    public function uploadEndorsement(Request $request, $id)
    {
        $request->validate([
            'endorsement_letter' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);
    
        $requirements = Requirement::findOrFail($id);
        $filePath = $request->file('endorsement_letter')->store('endorsement_letters');
        $requirements->endorsement_letter = $filePath;
        $requirements->save();
    
        // Check if all conditions for completion are met
        $this->checkRequirementCompletion($requirements);    

        return redirect()->back()->with('success', 'Endorsement letter uploaded.');
    }

    // Check if all conditions for completion are met
    private function checkRequirementCompletion($requirements)
    {
        // Check if both Waiver and Medical Certificate are accepted and the Endorsement Letter is uploaded
        if ($requirements->waiver_status_id == 2 && $requirements->medical_status_id == 2 && $requirements->endorsement_letter) {
            // Set the overall status_id to Accepted
            $requirements->status_id = 2; // Accepted
            $requirements->save();
        }
    }
    
}
