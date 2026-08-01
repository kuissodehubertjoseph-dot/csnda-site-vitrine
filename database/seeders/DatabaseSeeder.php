<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            PageSeeder::class,
            SettingSeeder::class,
            FiliereSeeder::class,
            NewsSeeder::class,
            GallerySeeder::class,
        ]);
    }
}
