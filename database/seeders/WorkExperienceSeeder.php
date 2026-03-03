<?php

namespace Database\Seeders;

use App\Models\WorkExperience;
use Illuminate\Database\Seeder;

class WorkExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $experiences = [
            [
                'title' => 'Senior Full Stack Developer',
                'position' => 'Senior Developer',
                'company' => 'Tech Solutions Co., Ltd.',
                'description' => 'Led development of enterprise web applications using Laravel and Vue.js. Managed a team of 5 developers, implemented CI/CD pipelines, and improved application performance by 40%. Architected scalable microservices and mentored junior developers.',
                'from' => '2022',
                'to' => 'Present',
            ],
            [
                'title' => 'Full Stack Developer',
                'position' => 'Developer',
                'company' => 'Digital Agency Cambodia',
                'description' => 'Developed and maintained multiple client projects including e-commerce platforms, CMS systems, and custom web applications. Worked with Laravel, Vue.js, and MySQL. Collaborated with designers and project managers to deliver high-quality solutions.',
                'from' => '2020',
                'to' => '2022',
            ],
            [
                'title' => 'Junior Web Developer',
                'position' => 'Junior Developer',
                'company' => 'StartUp Hub',
                'description' => 'Started career building responsive websites and web applications. Gained experience in PHP, JavaScript, HTML/CSS, and database management. Participated in agile development processes and code reviews.',
                'from' => '2018',
                'to' => '2020',
            ],
        ];

        foreach ($experiences as $experience) {
            WorkExperience::create($experience);
        }
    }
}
