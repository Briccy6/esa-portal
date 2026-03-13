<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolInformation;

class SchoolInformationSeeder extends Seeder
{
    public function run(): void
    {
        SchoolInformation::create([
            'school_name' => 'ESA Learning Portal',
            'address' => '123 Education Street, Learning City, Knowledge State',
            'phone' => '+250-788-123-456',
            'email' => 'info@esa-learning.rw',
            'website' => 'https://esa-learning.rw',
            'motto' => 'Excellence in Education Through Technology',
            'vision' => 'To be a leading educational institution that empowers students with quality education and modern skills for the future.',
            'mission' => 'To provide comprehensive, technology-driven education that prepares students for academic excellence and professional success.',
            'principal_name' => 'Dr. John Smith',
            'is_active' => true,
        ]);
    }
}
