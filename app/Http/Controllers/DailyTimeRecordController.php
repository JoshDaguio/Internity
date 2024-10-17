<?php

namespace App\Http\Controllers;

use App\Models\DailyTimeRecord;
use App\Models\AcceptedInternship;
use App\Models\InternshipHours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DailyTimeRecordController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $acceptedInternship = AcceptedInternship::where('student_id', $student->id)->first();
    
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
    
        // Check if today is a scheduled day and after start date
        $isScheduledDay = in_array($currentDateTime->format('l'), $scheduledDays) && $currentDateTime->gte($startDate);
    
        // Fetch today's DTR or create a new one if it doesn't exist
        $dailyRecord = DailyTimeRecord::firstOrCreate([
            'student_id' => $student->id,
            'log_date' => $currentDateTime->format('Y-m-d'),
        ], [
            'remaining_hours' => $internshipHours->hours,
        ]);
    
        // Calculate total work hours and remaining hours
        $totalWorkedHours = DailyTimeRecord::where('student_id', $student->id)->sum('total_hours_worked');
        $remainingHours = $internshipHours->hours - $totalWorkedHours;

        // Calculate completion percentage
        $completionPercentage = ($totalWorkedHours / $internshipHours->hours) * 100;
    
        // Update remaining_hours field in daily record
        $dailyRecord->remaining_hours = $remainingHours;
        $dailyRecord->save();
    
        // Estimate finish date based on remaining hours
        $estimatedFinishDate = $this->calculateFinishDate($remainingHours, $startDate, $scheduledDays);

        // // Pass current time as a string to the front-end
        // $apiTime = $currentDateTime->format('Y-m-d H:i:s');
    
        return view('daily_time_records.index', compact(
            'student','completionPercentage','dailyRecord', 'isScheduledDay', 'totalWorkedHours', 'remainingHours', 'estimatedFinishDate', 'currentDateTime', 'internshipHours', 'acceptedInternship'
        ));
    }

    public function getServerTime()
    {
        try {
            $response = Http::get('http://worldtimeapi.org/api/timezone/Asia/Manila');
            $currentDateTime = Carbon::parse($response->json('datetime'))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            $currentDateTime = Carbon::now(new \DateTimeZone('Asia/Manila'))->format('Y-m-d H:i:s');
        }

        return response()->json(['current_time' => $currentDateTime]);
    }

    
    private function calculateFinishDate($remainingHours, $startDate, $scheduledDays)
    {
        $estimatedDays = ceil($remainingHours / 8);
        $date = Carbon::parse($startDate);
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
                $morningWork = abs(Carbon::parse($logTimes['morning_out'])->diffInMinutes(Carbon::parse($logTimes['morning_in'])) / 60);
            }

            if (isset($logTimes['afternoon_in'], $logTimes['afternoon_out'])) {
                $afternoonWork = abs(Carbon::parse($logTimes['afternoon_out'])->diffInMinutes(Carbon::parse($logTimes['afternoon_in'])) / 60);
            }

            // Handle cases where only `morning_in` and `afternoon_out` are logged
            if (isset($logTimes['morning_in'], $logTimes['afternoon_out']) && !isset($logTimes['morning_out'], $logTimes['afternoon_in'])) {
                $totalWorkHours = abs(Carbon::parse($logTimes['afternoon_out'])->diffInMinutes(Carbon::parse($logTimes['morning_in'])) / 60);
            } else {
                $totalWorkHours = $morningWork + $afternoonWork;
            }


            // $totalWorkHours = $morningWork + $afternoonWork;

            // // Save the total hours worked for today
            // $dailyRecord->total_hours_worked = $totalWorkHours;
            $dailyRecord->total_hours_worked = $totalWorkHours;

            // Calculate the remaining hours across all records for this student
            $totalWorkedHours = DailyTimeRecord::where('student_id', $student->id)->sum('total_hours_worked');
            $internshipHours = InternshipHours::where('course_id', $student->course_id)->first();

            $remainingHours = $internshipHours->hours - $totalWorkedHours;
            $dailyRecord->remaining_hours = $remainingHours;

            // Save the updated daily record with recalculated fields
            $dailyRecord->save();
        }

        return redirect()->back()->with('success', 'Time logged successfully.');
    }
    


    

}
