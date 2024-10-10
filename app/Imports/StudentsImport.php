<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Profile;
use App\Models\Course;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\StudentApprovalMail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class StudentsImport implements ToModel, WithHeadingRow // Implement WithHeadingRow
{
    public function model(array $row)
    {
        // Ensure all keys are lowercase for consistency
        $row = array_change_key_case($row, CASE_LOWER);

        // Check if all required fields are present
        if (!isset($row['name'], $row['email'], $row['id_number'], $row['course'])) {
            Log::error('Missing required fields in the row: ', $row);
            return null;
        }

        // Split the name column into last and first name
        $nameParts = explode(',', $row['name']);
        $lastName = trim($nameParts[0]);
        $firstName = trim($nameParts[1] ?? '');

        // Validate and format email and ID number
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@auf.edu.ph$/', $row['email']) || !preg_match('/^\d{2}-\d{4}-\d{3}$/', $row['id_number'])) {
            Log::error('Invalid email or ID number format: ', $row);
            return null;
        }

        // Find course ID by matching course code or name
        $course = Course::where('course_code', $row['course'])
            ->orWhere('course_name', $row['course'])
            ->first();

        if (!$course) {
            Log::error('Course not found: ' . $row['course']);
            return null;
        }

        // Check if a profile with the same ID number already exists
        $existingProfile = Profile::where('id_number', $row['id_number'])->first();
        if ($existingProfile) {
            Log::warning('Profile with ID number already exists: ', $row);
            // Skip this row without stopping the entire process
            return null;
        }

        // Create profile
        $profile = Profile::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'id_number' => $row['id_number'],
        ]);

        // Generate a random password
        $password = 'aufCCSInternship' . Str::random(5);

        // Get the current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Create student account
        $student = User::create([
            'name' => $firstName,
            'email' => $row['email'],
            'password' => Hash::make($password),
            'role_id' => 5, // Student role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
            'course_id' => $course->id,
            'academic_year_id' => $currentAcademicYear->id,
        ]);

        // Send email with login details
        Mail::to($student->email)->send(new StudentApprovalMail(
            $student->name,
            $student->email,
            $password,
            $student->course->course_name
        ));

        return $student;
    }
}
