<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Web Application'],
            ['name' => 'Mobile App'],
            ['name' => 'API Development'],
            ['name' => 'E-Commerce'],
            ['name' => 'CMS'],
            ['name' => 'Open Source'],
            ['name' => 'Dashboard'],
            ['name' => 'Landing Page'],
        ];

        foreach ($types as $type) {
            ProjectType::create($type);
        }
    }
}
