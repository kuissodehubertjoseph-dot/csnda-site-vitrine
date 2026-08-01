<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('accueil_accroche', "Former des jeunes filles enracinées dans la foi, la discipline et l'excellence académique, au cœur de Cotonou.");
        Setting::set('contact_telephone', '+229 21 30 00 00');
        Setting::set('contact_email', 'contact@csnda-cotonou.bj');
        Setting::set('contact_adresse', 'Cotonou, Bénin');
        Setting::set('contact_map_embed', null);
    }
}
