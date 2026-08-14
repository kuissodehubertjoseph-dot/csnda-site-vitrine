<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        $anneeScolaire = config('ecole.annee_scolaire_courante');
        $sexe = $this->faker->randomElement(['M', 'F']);

        return [
            'nom' => strtoupper($this->faker->lastName()),
            'prenoms' => $sexe === 'M' ? $this->faker->firstNameMale() : $this->faker->firstNameFemale(),
            'date_naissance' => $this->faker->dateTimeBetween('-14 years', '-3 years')->format('Y-m-d'),
            'lieu_naissance' => $this->faker->randomElement(['Cotonou', 'Porto-Novo', 'Abomey-Calavi', 'Parakou', 'Ouidah']),
            'sexe' => $sexe,
            'classe' => $this->faker->randomElement(config('ecole.classes')),
            'telephone' => $this->faker->numerify('01#### ####'),
            'photo' => null,
            'annee_scolaire' => $anneeScolaire,
            'statut' => 'actif',
        ];
    }
}
