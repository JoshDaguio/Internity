<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        DB::table('account_statuses')->insert([
            ['id' => 1, 'status' => 'Active'], // Accounts with Active status can login
            ['id' => 2, 'status' => 'Inactive'], // Accounts with Inactive status can't login
            ['id' => 3, 'status' => 'Pending'], // Accounts with Pending status are for approval on registration
        ]);
    }
}
