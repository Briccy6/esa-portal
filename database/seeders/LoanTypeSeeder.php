<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
 use App\Models\LoanType;

class LoanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   

public function run()
{
    LoanType::insert([
        ['name' => 'Registration', 'total_amount' => 5000],
        ['name' => 'School Fees', 'total_amount' => 120000],
        ['name' => 'Practical', 'total_amount' => 15000],
        ['name' => 'Trip', 'total_amount' => 10000],
    ]);
}

}
