<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'first_name' => 'Excellent',
            'last_name' => 'Servant Academy',
            'email' => 'esa@esa.com',
            'password' => Hash::make('@Esa123456789'),
        ]);
    }
}
