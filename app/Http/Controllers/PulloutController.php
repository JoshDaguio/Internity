<?php

namespace App\Http\Controllers;

use App\Models\Pullout;
use App\Models\User;
use App\Models\AcceptedInternship;
use App\Models\Penalty;
use App\Models\DailyTimeRecord;
use App\Models\AcademicYear;
use App\Models\PenaltiesAwarded;
use App\Models\InternshipHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PulloutController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::all();
        $selectedYearId = $request->input('academic_year_id', AcademicYear::where('is_current', true)->first()->id);

        $pullouts = Pullout::where('academic_year_id', $selectedYearId)
            ->with(['students.profile', 'company', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pullouts.index', compact('pullouts', 'academicYears', 'selectedYearId'));
    }

    public function getStudentsByCompany($companyId)
    {
        $students = AcceptedInternship::where('company_id', $companyId)
            ->whereHas('student', function ($query) {
                $query->where('pullout_count', '>', 0);
            })
            ->with('student:id,name,pullout_count')
            ->get()
            ->pluck('student');

        return response()->json($students);
    }

    public function companyIndex(Request $request)
    {
        $company = Auth::user();
        $academicYears = AcademicYear::all();
        $selectedYearId = $request->input('academic_year_id', AcademicYear::where('is_current', true)->first()->id);
    
        $pullouts = Pullout::with(['students.profile', 'creator'])
            ->where('company_id', $company->id)
            ->where('academic_year_id', $selectedYearId)
            ->orderBy('created_at', 'desc')
            ->get();
    
        return view('pullouts.company_index', compact('pullouts', 'academicYears', 'selectedYearId'));
    }

    public function create()
    {
        $companies = User::where('role_id', 4)->where('status_id', 1)->get();
        return view('pullouts.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:users,id',
            'students' => 'required|array',
            'pullout_date' => 'required|date',
            'excuse_detail' => 'required|string',
        ]);
    
        $currentAcademicYear = AcademicYear::where('is_current', true)->firstOrFail();
        $company = User::findOrFail($request->company_id);
    
        $pullout = Pullout::create([
            'company_id' => $company->id,
            'created_by' => Auth::id(),
            'pullout_date' => Carbon::parse($request->pullout_date),
            'status' => 'pending',
            'excuse_detail' => $request->excuse_detail,
            'academic_year_id' => $currentAcademicYear->id,
        ]);
    
        $eligibleStudents = $this->getStudentsByCompany($company->id)->getData();

        foreach ($request->students as $studentId) {
            if (collect($eligibleStudents)->where('id', $studentId)->isNotEmpty()) {
                $pullout->students()->attach($studentId);
                User::where('id', $studentId)->decrement('pullout_count');
            }
        }
    
        Mail::to($company->email)->send(new \App\Mail\PulloutRequestNotification($pullout));
    
        return redirect()->route('pullouts.index')->with('success', 'Pullout request sent successfully.');
    }

    public function showRespondForm(Pullout $pullout)
    {
        if (Auth::id() !== $pullout->company_id) {
            return redirect()->route('pullouts.companyIndex')->with('error', 'Unauthorized access.');
        }

        return view('pullouts.respond', compact('pullout'));
    }

    public function respond(Request $request, Pullout $pullout)
    {
        if (Auth::id() !== $pullout->company_id) {
            return redirect()->route('pullouts.companyIndex')->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'company_remark' => 'nullable|string',
        ]);

        $pullout->update([
            'status' => $request->status,
            'company_remark' => $request->company_remark,
        ]);

        if ($request->status === 'accepted') {
            $penalty = Penalty::where('violation', 'Excused Absence')->first();
    
            foreach ($pullout->students as $student) {
                if ($student->pullout_count > 0) {
                    $dailyRecord = DailyTimeRecord::where('student_id', $student->id)->latest('log_date')->first();
    
                    if (!$dailyRecord) {
                        $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();
                        $remainingHours = $internshipHours->hours ?? 0;
    
                        $dailyRecord = DailyTimeRecord::create([
                            'student_id' => $student->id,
                            'log_date' => Carbon::now(),
                            'remaining_hours' => $remainingHours,
                            'total_hours_worked' => 0,
                        ]);
                    }
    
                    $penaltyHours = $penalty->penalty_hours;
                    $newRemainingHours = $dailyRecord->remaining_hours + $penaltyHours;
    
                    PenaltiesAwarded::create([
                        'student_id' => $student->id,
                        'penalty_id' => $penalty->id,
                        'dtr_id' => $dailyRecord->id,
                        'penalty_hours' => $penaltyHours,
                        'awarded_date' => Carbon::now(),
                        'remarks' => 'Pullout approved excused absence',
                    ]);
    
                    $dailyRecord->remaining_hours = $newRemainingHours;
                    $dailyRecord->save();
    
                    $student->decrement('pullout_count');
                }
            }
        }

        return redirect()->route('pullouts.companyIndex')->with('success', 'Pullout request has been ' . $request->status . '.');
    }
}
