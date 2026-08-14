<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Crée le compte administrateur (directeur/secrétaire) par défaut.
     * Identifiants par défaut — à changer après la première connexion.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@notredameapotres.bj'],
            [
                'name' => 'Administration NDA',
                'password' => 'password',
            ]
        );
    }
}
