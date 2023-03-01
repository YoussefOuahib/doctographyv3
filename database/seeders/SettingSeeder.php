<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'key' => 'is_rate_fixed',
            'value' => true,
        ]);
        Setting::create([
            'key' => 'rate',
            'value' => 250,
        ]);
       

        Setting::create([
            'key' => "name",
            'value' => 'Yasmina Ksikes',
        ]);
        Setting::create([
            'key' => "speciality",
            'value' => 'Spécialiste en Orthopédie Dento-Faciale et Orthodentie',
        ]);
        Setting::create([
            'key' => 'address',
            'value' => 'App 3 2 eme étage mhamid 1 c num 283. ( au dessus du café boughaz)
            Mhamid 40054 Marrakech Maroc',
        ]);
        Setting::create([
            'key' => 'diplome',
            'value' => 'Diplomée de la faculté de paris',
        ]);
        Setting::create([
            'key' => 'inp',
            'value' => '0741711919',
        ]);
        Setting::create([
            'key' => 'patente',
            'value' => '45126356',
        ]);
        Setting::create([
            'key' => 'if',
            'value' => '40104704',
        ]);
        Setting::create([
            'key' => 'cnss',
            'value' => '8431570',
        ]);
        Setting::create([
            'key' => 'ice',
            'value' => '001634865000047',
        ]);
        Setting::create([
            'key' => 'waiting',
            'value' => 0,
        ]);
    }
}
