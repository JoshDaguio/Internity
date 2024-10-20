<?php

namespace App\Http\Controllers;

use App\Models\DailyTimeRecord;
use App\Models\AcceptedInternship;
use App\Models\InternshipHours;
use App\Models\Penalty;
use App\Models\PenaltiesAwarded;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;


class DailyTimeRecordController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();
        $penaltiesAwarded = PenaltiesAwarded::where('student_id', $student->id)->get();

        // Ensure the student has an accepted internship
        if (!$acceptedInternship) {
            return view('daily_time_records.index', ['noInternship' => true]);
        }

        // Fetch internship hours
        $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();

        // Get current date from API
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
            $currentDateTime = Carbon::parse($response->json('datetime'));
        } catch (\Exception $e) {
            $currentDateTime = Carbon::now(new \DateTimeZone('Asia/Manila'));
        }

        $startDate = Carbon::parse($acceptedInternship->start_date);

        // Check if student is irregular
        if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
            // Use custom schedule for irregular students
            $schedule = $acceptedInternship->custom_schedule;
            $scheduledDays = array_keys($schedule); // Extract the days based on keys
        } else {
            // Use regular schedule for regular students
            $schedule = json_decode($acceptedInternship->schedule, true);

            // Determine scheduled days based on work type
            if ($acceptedInternship->work_type === 'Hybrid') {
                // Combine onsite and remote days for hybrid schedules
                $scheduledDays = array_merge($schedule['onsite_days'], $schedule['remote_days']);
            } else {
                // For On-site or Remote, use the standard 'days' array
                $scheduledDays = $schedule['days'];
            }
        }

        // Check if today is a scheduled day and after the start date
        $isScheduledDay = in_array($currentDateTime->format('l'), $scheduledDays) && $currentDateTime->gte($startDate);

        // Fetch the latest Daily Time Record for this student (based on the latest log date)
        $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
            ->orderBy('log_date', 'desc')
            ->first();

        // Fetch or create today's record
        $todayRecord = DailyTimeRecord::firstOrCreate([
            'student_id' => $student->id,
            'log_date' => $currentDateTime->format('Y-m-d'),
        ], [
            'remaining_hours' => $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours,
        ]);

        // Calculate total work hours and remaining hours
        $totalWorkedHours = DailyTimeRecord::where('student_id', $student->id)->sum('total_hours_worked');
        $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours - $totalWorkedHours;

        // Calculate completion percentage
        $completionPercentage = ($totalWorkedHours / $remainingHours) * 100;

        // Estimate finish date based on remaining hours
        $estimatedFinishDate = $this->calculateFinishDate($remainingHours, $startDate, $scheduledDays);

        return view('daily_time_records.index', compact(
            'student', 'completionPercentage', 'todayRecord', 'latestDailyRecord', 'isScheduledDay', 'totalWorkedHours', 'remainingHours', 'estimatedFinishDate', 'currentDateTime', 'internshipHours', 'acceptedInternship', 'penaltiesAwarded'
        ));
    }


    
    private function calculateFinishDate($remainingHours, $startDate, $scheduledDays)
    {
        $estimatedDays = ceil($remainingHours / 8);
        // $date = Carbon::parse($startDate);
        $date = Carbon::now();  // Start from today
        $daysWorked = 0;
    
        while ($daysWorked < $estimatedDays) {
            if (in_array($date->format('l'), $scheduledDays)) {
                $daysWorked++;
            }
            $date->addDay();
        }
    
        return $date;
    }

    public function logTime(Request $request)
    {
        $student = Auth::user();
        $type = $request->route('type'); // Ensure 'type' is passed correctly as 'morning_in', 'morning_out', etc.

        // For testing, allow custom time via request (useful for testing during off-hours)
        $testTime = $request->input('test_time');


        // try {
        //     $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
        //     $currentTime = Carbon::parse($response->json('datetime'))->format('h:i A');
        // } catch (\Exception $e) {
        //     $currentTime = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('h:i A');
        // }

        try {
            if ($testTime) {
                $currentTime = Carbon::parse($testTime)->format('h:i A');
            } else {
                $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
                $currentTime = Carbon::parse($response->json('datetime'))->format('h:i A');
            }
        } catch (\Exception $e) {
            $currentTime = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('h:i A');
        }

        // Ensure the log type is valid
        $validTypes = ['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out'];
        if (!in_array($type, $validTypes)) {
            return redirect()->back()->with('error', 'Invalid log type.');
        }

        // Fetch the student's internship details
        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();
        if (!$acceptedInternship) {
            return redirect()->back()->with('error', 'No internship found.');
        }

        // Determine start time based on the student's schedule
        $dayOfWeek = Carbon::now()->format('l'); // Get the current day of the week (e.g., 'Monday')
        $startTime = $acceptedInternship->start_time;

        if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = is_array($acceptedInternship->custom_schedule) 
                ? $acceptedInternship->custom_schedule 
                : json_decode($acceptedInternship->custom_schedule, true);

            if (isset($customSchedule[$dayOfWeek])) {
                $startTime = $customSchedule[$dayOfWeek]['start'];
            }
        }

        // Compare the start time and check for lateness
        $isLate = Carbon::parse($currentTime)->gt(Carbon::parse($startTime)); // If current time is greater than start time
        $lateMinutes = Carbon::parse($currentTime)->diffInMinutes(Carbon::parse($startTime), false);

        $dailyRecord = DailyTimeRecord::firstOrCreate([
            'student_id' => $student->id,
            'log_date' => Carbon::now()->format('Y-m-d'),
        ]);

        // Decode the existing log_times JSON, or create a new array if it's null
        $logTimes = $dailyRecord->log_times ? json_decode($dailyRecord->log_times, true) : [];

        // Ensure the 'type' is set correctly in the logTimes array
        if (!isset($logTimes[$type])) {
            $logTimes[$type] = $currentTime;
            $dailyRecord->log_times = json_encode($logTimes);
            $dailyRecord->save();

            // Recalculate total work hours and adjust remaining hours
            $morningWork = 0;
            $afternoonWork = 0;

            if (isset($logTimes['morning_in'], $logTimes['morning_out'])) {
                $morningWork = Carbon::parse($logTimes['morning_in'])->diffInHours(Carbon::parse($logTimes['morning_out']), false);
            }

            if (isset($logTimes['afternoon_in'], $logTimes['afternoon_out'])) {
                $afternoonWork = Carbon::parse($logTimes['afternoon_in'])->diffInHours(Carbon::parse($logTimes['afternoon_out']), false);
            }

            // Handle cases where only `morning_in` and `afternoon_out` are logged
            if (isset($logTimes['morning_in'], $logTimes['afternoon_out']) && !isset($logTimes['morning_out'], $logTimes['afternoon_in'])) {
                $totalWorkHours = Carbon::parse($logTimes['morning_in'])->diffInHours(Carbon::parse($logTimes['afternoon_out']), false);
            } else {
                $totalWorkHours = $morningWork + $afternoonWork;
            }


            $dailyRecord->total_hours_worked = $totalWorkHours;

            // Calculate the remaining hours across all records for this student
            $totalWorkedHours = DailyTimeRecord::where('student_id', $student->id)->sum('total_hours_worked');
            $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();

            // Fetch the latest remaining hours from the last daily record
            $latestRecord = DailyTimeRecord::where('student_id', $student->id)
                ->orderBy('log_date', 'desc')
                ->first();
                
            $remainingHours = $latestRecord ? $latestRecord->remaining_hours : $internshipHours->hours;

            // If the student is late, apply the penalty
            if ($isLate) {
                if ($lateMinutes < 30) {
                    // Less than 30 minutes late: 1 hour for every 10 minutes late
                    $penaltyHours = ceil($lateMinutes / 10);

                    $penalty = Penalty::where('violation', 'Tardiness (Less than 30 minutes)')->first();
                } else {
                    // More than 1 hour but less than 4 hours: 1 day (8 hours)
                    $penaltyHours = 8;

                    $penalty = Penalty::where('violation', 'Tardiness (More than 1 hour but less than 4 hours)')->first();
                }

                if (isset($penalty)) {

                    // Ensure penalty hours are positive before saving
                    $penaltyHours = abs($penaltyHours);

                    // Award the penalty
                    PenaltiesAwarded::create([
                        'student_id' => $student->id,
                        'penalty_id' => $penalty->id,
                        'dtr_id' => $dailyRecord->id,
                        'penalty_hours' => $penaltyHours,
                        'awarded_date' => Carbon::now(),
                        'remarks' => 'Automatic penalty for tardiness',
                    ]);

                    // Update remaining hours after penalty
                    $dailyRecord->remaining_hours += $penaltyHours;  // Add penalty hours positively
                    $dailyRecord->save();
                }
            }

            return redirect()->back()->with('success', 'Time logged successfully.');
        }

        return redirect()->back()->with('error', 'Time already logged for this period.');
    }
    

    public function reports(Request $request)
    {
        $student = Auth::user();
        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();

        // Fetch all penalties
        $penalties = Penalty::all();
        $penaltiesAwarded = PenaltiesAwarded::where('student_id', $student->id)->get();


        // Ensure the student has an accepted internship
        if (!$acceptedInternship) {
            return view('reports', ['noInternship' => true]);
        }

        // Fetch internship hours
        $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();

        // Fetch all Daily Time Records for this student
        $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();

        // Fetch the latest Daily Time Record for this student (even if it's not today)
        $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
            ->orderBy('log_date', 'desc')
            ->first();

        // Fallback for remaining hours if there is no DTR yet
        $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours;

        // Get current date and start date for filtering
        $currentDate = Carbon::now();
        $startDate = Carbon::parse($acceptedInternship->start_date);

        // Calculate total worked hours
        $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
        $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours;

        // Fetch the schedule
        $schedule = $acceptedInternship->schedule;

        // Initialize $scheduledDays as an empty array in case it's not set later
        $scheduledDays = [];

        // If the schedule is not an array (i.e., it's stored as JSON), decode it
        if (!is_array($schedule)) {
            $schedule = json_decode($schedule, true);
        }

        // Check if the student is irregular and if there is a custom schedule
        if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = $acceptedInternship->custom_schedule;

            // If custom schedule is not an array, decode it
            if (!is_array($customSchedule)) {
                $schedule = json_decode($customSchedule, true);
            } else {
                $schedule = $customSchedule; // Use the custom schedule directly
            }

            // Extract the days from the custom schedule
            $scheduledDays = array_keys($schedule);

        } else {
            // Handle Hybrid schedules
            if ($acceptedInternship->work_type === 'Hybrid') {
                $scheduledDays = array_merge($schedule['onsite_days'], $schedule['remote_days']);
            } else {
                // For On-site or Remote, use the standard 'days' array
                $scheduledDays = $schedule['days'] ?? [];
            }
        }

        // Filter the logs by month based on the user's input or current month
        $selectedMonth = $request->input('month', $currentDate->month); // Default to current month

        // Generate the range of months from the start date to the current date
        $monthsRange = collect();
        $monthIterator = $startDate->copy()->startOfMonth();
        while ($monthIterator->lte($currentDate)) {
            $monthsRange->push([
                'month' => $monthIterator->format('m'),
                'monthName' => $monthIterator->format('F') // Full month name
            ]);
            $monthIterator->addMonth();
        }

        // Only fetch the days of the selected month for display
        // Filter the logs by the selected month
        $filteredRecords = $dailyRecords->filter(function ($record) use ($selectedMonth) {
            return Carbon::parse($record->log_date)->month == $selectedMonth;
        });
        
        // Get the days for the selected month for the whole month to display in the table
        $filteredDates = collect();
        $monthStart = Carbon::createFromDate($startDate->year, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Adjust monthStart for the first month if it is the month of the start date
        if ($selectedMonth == $startDate->month) {
            $monthStart = $startDate;
        }
        
        // Show only up to today for the current month
        if ($selectedMonth == $currentDate->month) {
            $monthEnd = $currentDate;
        }

        while ($monthStart->lte($monthEnd)) {
            // Only include scheduled days
            $dayOfWeek = $monthStart->format('l');
            if (in_array($dayOfWeek, $scheduledDays)) {
                $filteredDates->push($monthStart->copy());
            }
            $monthStart->addDay();
        }

        // Calculate completion percentage
        $completionPercentage = ($totalWorkedHours / $remainingHours) * 100;


        // Estimate the finish date
        $estimatedFinishDate = $this->calculateFinishDate($remainingHours, $currentDate, $scheduledDays);

        return view('daily_time_records.reports', compact(
            'penaltiesAwarded', 'completionPercentage','totalWorkedHours', 'penalties', 'student', 'acceptedInternship', 'internshipHours', 'filteredRecords', 'schedule', 'currentDate', 'startDate', 'selectedMonth', 'scheduledDays', 'remainingHours', 'estimatedFinishDate', 'filteredDates', 'monthsRange'
        ));
    }


    public function studentDTR($studentId, Request $request)
    {
        // Fetch the student using the provided $studentId
        $student = User::findOrFail($studentId);
        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();

        // Fetch all penalties
        $penalties = Penalty::all();
        $penaltiesAwarded = PenaltiesAwarded::where('student_id', $student->id)->get();


        // Ensure the student has an accepted internship
        if (!$acceptedInternship) {
            return view('reports', ['noInternship' => true]);
        }

        // Fetch internship hours
        $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();

        // Fetch all Daily Time Records for this student
        $dailyRecords = DailyTimeRecord::where('student_id', $student->id)->get();

        // Fetch the latest Daily Time Record for this student (even if it's not today)
        $latestDailyRecord = DailyTimeRecord::where('student_id', $student->id)
            ->orderBy('log_date', 'desc')
            ->first();

        // Fallback for remaining hours if there is no DTR yet
        $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours;

        // Get current date and start date for filtering
        $currentDate = Carbon::now();
        $startDate = Carbon::parse($acceptedInternship->start_date);

        // Calculate total worked hours
        $totalWorkedHours = $dailyRecords->sum('total_hours_worked');
        $remainingHours = $latestDailyRecord ? $latestDailyRecord->remaining_hours : $internshipHours->hours;

        // Fetch the schedule
        $schedule = $acceptedInternship->schedule;

        // Initialize $scheduledDays as an empty array in case it's not set later
        $scheduledDays = [];

        // If the schedule is not an array (i.e., it's stored as JSON), decode it
        if (!is_array($schedule)) {
            $schedule = json_decode($schedule, true);
        }

        // Check if the student is irregular and if there is a custom schedule
        if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = $acceptedInternship->custom_schedule;

            // If custom schedule is not an array, decode it
            if (!is_array($customSchedule)) {
                $schedule = json_decode($customSchedule, true);
            } else {
                $schedule = $customSchedule; // Use the custom schedule directly
            }

            // Extract the days from the custom schedule
            $scheduledDays = array_keys($schedule);

        } else {
            // Handle Hybrid schedules
            if ($acceptedInternship->work_type === 'Hybrid') {
                $scheduledDays = array_merge($schedule['onsite_days'], $schedule['remote_days']);
            } else {
                // For On-site or Remote, use the standard 'days' array
                $scheduledDays = $schedule['days'] ?? [];
            }
        }

        // Filter the logs by month based on the user's input or current month
        $selectedMonth = $request->input('month', $currentDate->month); // Default to current month

        // Generate the range of months from the start date to the current date
        $monthsRange = collect();
        $monthIterator = $startDate->copy()->startOfMonth();
        while ($monthIterator->lte($currentDate)) {
            $monthsRange->push([
                'month' => $monthIterator->format('m'),
                'monthName' => $monthIterator->format('F') // Full month name
            ]);
            $monthIterator->addMonth();
        }

        // Only fetch the days of the selected month for display
        // Filter the logs by the selected month
        $filteredRecords = $dailyRecords->filter(function ($record) use ($selectedMonth) {
            return Carbon::parse($record->log_date)->month == $selectedMonth;
        });
        
        // Get the days for the selected month for the whole month to display in the table
        $filteredDates = collect();
        $monthStart = Carbon::createFromDate($startDate->year, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Adjust monthStart for the first month if it is the month of the start date
        if ($selectedMonth == $startDate->month) {
            $monthStart = $startDate;
        }
        
        // Show only up to today for the current month
        if ($selectedMonth == $currentDate->month) {
            $monthEnd = $currentDate;
        }

        while ($monthStart->lte($monthEnd)) {
            // Only include scheduled days
            $dayOfWeek = $monthStart->format('l');
            if (in_array($dayOfWeek, $scheduledDays)) {
                $filteredDates->push($monthStart->copy());
            }
            $monthStart->addDay();
        }

        // Estimate the finish date
        $estimatedFinishDate = $this->calculateFinishDate($remainingHours, $currentDate, $scheduledDays);

        return view('daily_time_records.student-dtr', compact(
            'penaltiesAwarded', 'penalties', 'student', 'acceptedInternship', 'internshipHours', 'filteredRecords', 'schedule', 'currentDate', 'startDate', 'selectedMonth', 'scheduledDays', 'remainingHours', 'estimatedFinishDate', 'filteredDates', 'monthsRange'
        ));
    }


    public function generateReportPDF(Request $request)
    {
        $student = Auth::user();
        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();
    
        // Ensure the student has an accepted internship
        if (!$acceptedInternship) {
            return redirect()->back()->with('error', 'No internship found.');
        }
    
        $currentDate = Carbon::now();
        $startDate = Carbon::parse($acceptedInternship->start_date);
    
        // Fetch the schedule
        $schedule = $acceptedInternship->schedule;
        if (!is_array($schedule)) {
            $schedule = json_decode($schedule, true);
        }
    
        // Fetch the custom schedule for irregular students
        if ($student->profile->is_irregular && $acceptedInternship->custom_schedule) {
            $customSchedule = $acceptedInternship->custom_schedule;
            if (!is_array($customSchedule)) {
                $customSchedule = json_decode($customSchedule, true);
            }
            $scheduledDays = array_keys($customSchedule);
        } else {
            $customSchedule = null;
            $scheduledDays = $schedule['days'] ?? [];
        }
    
        // Get the penalties awarded in the selected month
        $penaltiesAwarded = PenaltiesAwarded::where('student_id', $student->id)
            ->whereMonth('awarded_date', $request->input('month'))
            ->get();
    
        // Fetch Daily Time Records for the selected month
        $dailyRecords = DailyTimeRecord::where('student_id', $student->id)
            ->whereMonth('log_date', $request->input('month'))
            ->get();
    
        // Filter the logs by month and selected month
        $selectedMonth = $request->input('month', $currentDate->month);
        $filteredRecords = $dailyRecords->filter(function ($record) use ($selectedMonth) {
            return Carbon::parse($record->log_date)->month == $selectedMonth;
        });
    
        // Get the days for the selected month
        $filteredDates = collect();
        $monthStart = Carbon::createFromDate($startDate->year, $selectedMonth, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
    
        // Adjust for the start month if it's the same as the internship start month
        if ($selectedMonth == $startDate->month) {
            $monthStart = $startDate;
        }
    
        // Limit to today if the selected month is the current month
        if ($selectedMonth == $currentDate->month) {
            $monthEnd = $currentDate;
        }
    
        while ($monthStart->lte($monthEnd)) {
            $dayOfWeek = $monthStart->format('l');
            if (in_array($dayOfWeek, $scheduledDays)) {
                $filteredDates->push($monthStart->copy());
            }
            $monthStart->addDay();
        }
    
        // Example call to calculateAbsences
        $totalAbsences = $this->calculateAbsences($customSchedule, $schedule, $dailyRecords, $filteredDates);

    
        // Tardiness and penalties calculation
        $totalTardiness = $this->calculateTardiness($penaltiesAwarded);
        $totalMakeupHours = $penaltiesAwarded->sum('penalty_hours');
    
        // Generate PDF
        $pdf = Pdf::loadView('daily_time_records.pdf_report', [
            'student' => $student,
            'acceptedInternship' => $acceptedInternship,
            'schedule' => $schedule,
            'customSchedule' => $customSchedule,
            'dailyRecords' => $dailyRecords,
            'filteredDates' => $filteredDates,
            'scheduledDays' => $scheduledDays,
            'totalAbsences' => $totalAbsences,
            'totalTardiness' => $totalTardiness,
            'totalMakeupHours' => $totalMakeupHours,
            'month' => Carbon::createFromDate(null, $request->input('month'))->format('F')
        ]);
    
        return $pdf->download('DTR-Report-' . $student->profile->first_name . '.pdf');
    }
    
     

    private function calculateTardiness($penaltiesAwarded)
    {
        return $penaltiesAwarded->filter(function ($penalty) {
            return str_contains($penalty->penalty->violation, 'Tardiness');
        })->count();
    }    

    private function calculateAbsences($customSchedule, $schedule, $dailyRecords, $filteredDates)
    {
        $absences = 0;
        
        // Determine if custom or regular schedule is used
        if (!empty($customSchedule)) {
            $scheduleDays = array_keys($customSchedule); // Extract custom schedule days (e.g., Monday, Tuesday, etc.)
        } else {
            $scheduleDays = $schedule['days'] ?? [];
        }

        // Loop through the filtered dates (scheduled days in the selected month)
        foreach ($filteredDates as $date) {
            $dayOfWeek = $date->format('l'); // Get the name of the day (e.g., Monday)
            
            // Check if this day is part of the schedule
            if (in_array($dayOfWeek, $scheduleDays)) {
                // Check if a record exists for this day in dailyRecords
                $record = $dailyRecords->where('log_date', $date->format('Y-m-d'))->first();
                
                // If no record exists for this scheduled day, count as an absence
                if (!$record) {
                    $absences++;
                }
            }
        }

        return $absences;
    }



}
