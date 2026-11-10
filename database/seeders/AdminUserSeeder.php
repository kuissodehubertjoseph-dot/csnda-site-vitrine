<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('administration.email');
        $password = config('administration.password');

        if (! is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false
            || ! is_string($password) || $password === '') {
            throw new InvalidArgumentException('CORTEX_ADMIN_EMAIL et CORTEX_ADMIN_PASSWORD doivent être configurés pour créer le compte global.');
        }

        $utilisateur = User::where('email', $email)->first()
            ?? User::where('name', 'Administration Cortex Bénin TV')
                ->where('role', User::ROLE_DEVELOPPEUR)
                ->whereNull('etablissement_id')
                ->first();

        if ($utilisateur) {
            $utilisateur->update([
                'name' => 'Administration Cortex Bénin TV',
                'email' => $email,
                'password' => $password,
                'role' => User::ROLE_DEVELOPPEUR,
                'etablissement_id' => null,
            ]);

            return;
        }

        User::create([
            'name' => 'Administration Cortex Bénin TV',
            'email' => $email,
            'password' => $password,
            'role' => User::ROLE_DEVELOPPEUR,
            'etablissement_id' => null,
        ]);
    }
}
