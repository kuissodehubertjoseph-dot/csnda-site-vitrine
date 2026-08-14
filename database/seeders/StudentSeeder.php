<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Quelques élèves de test répartis sur différentes classes.
     */
    public function run(): void
    {
        Student::factory()->count(25)->create();
    }
}
