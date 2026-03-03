<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educations = [
            [
                'title' => 'Bachelor of Computer Science',
                'major' => 'Software Engineering',
                'institution' => 'Royal University of Phnom Penh',
                'description' => 'Graduated with honors. Focused on software development, algorithms, data structures, and database systems. Completed capstone project on web application development using modern frameworks.',
                'from' => '2014',
                'to' => '2018',
            ],
            [
                'title' => 'Full Stack Web Development Certificate',
                'major' => 'Web Development',
                'institution' => 'Online Certification - Udemy',
                'description' => 'Completed comprehensive courses on modern web development including Laravel, Vue.js, React, Node.js, and cloud deployment. Hands-on projects and real-world applications.',
                'from' => '2019',
                'to' => '2020',
            ],
            [
                'title' => 'AWS Cloud Practitioner',
                'major' => 'Cloud Computing',
                'institution' => 'Amazon Web Services',
                'description' => 'Certified in AWS cloud fundamentals including EC2, S3, RDS, Lambda, and cloud architecture best practices.',
                'from' => '2021',
                'to' => '2021',
            ],
        ];

        foreach ($educations as $education) {
            Education::create($education);
        }
    }
}
