<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyApprovalMail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class CompanyImport implements ToModel, WithHeadingRow 
{
    public function model(array $row)
    {
        // Ensure all keys are lowercase for consistency
        $row = array_change_key_case($row, CASE_LOWER);

        // Check if all required fields are present
        if (!isset($row['company_name'], $row['email'], $row['contact_person'])) {
            Log::error('Missing required fields in the row: ', $row);
            return null;
        }

        // Split the contact person name into last and first name
        $nameParts = explode(',', $row['contact_person']);
        $lastName = trim($nameParts[0]);
        $firstName = trim($nameParts[1] ?? '');

        // Validate email format
        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            Log::error('Invalid email format: ', $row);
            return null;
        }

        // Check if the email already exists
        $existingUser = User::where('email', $row['email'])->first();
        if ($existingUser) {
            Log::warning('User with email already exists: ', $row);
            return null;
        }

        // Create profile for the contact person
        $profile = Profile::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'id_number' => null, // No ID number for companies
        ]);

        // Generate a random password
        $password = 'aufCCSInternshipCompany' . Str::random(5);

        // Create company account
        $company = User::create([
            'name' => $row['company_name'], // Company name
            'email' => $row['email'],
            'password' => Hash::make($password),
            'role_id' => 4, // Company role
            'status_id' => 1, // Active status
            'profile_id' => $profile->id,
        ]);

        // Send email with login details
        Mail::to($company->email)->send(new CompanyApprovalMail(
            $company->name,
            $company->email,
            $password
        ));

        return $company;
    }
}
