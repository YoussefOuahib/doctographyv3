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
            'value' => 'Dr. Bou-Souab Mouna',
        ]);
        Setting::create([
            'key' => "speciality",
            'value' => 'Implantologie-Orthodontie-Blanchiment-Soin-Chirurgie-Radiographie',
        ]);
        Setting::create([
            'key' => 'address',
            'value' => 'App 3 2 eme étage mhamid 1 c num 283. ( au dessus du café boughaz)
            Mhamid 40054 Marrakech Maroc',
        ]);
        Setting::create([
            'key' => 'diplome',
            'value' => "Diplomée de l'université hassan II de Médecine Dentaire casablanca",
        ]);
        Setting::create([
            'key' => 'inp',
            'value' => '074177478',
        ]);
        Setting::create([
            'key' => 'patente',
            'value' => '45126356',
        ]);
        Setting::create([
            'key' => 'if',
            'value' => '40441548',
        ]);
        Setting::create([
            'key' => 'cnss',
            'value' => '8431570',
        ]);
        Setting::create([
            'key' => 'ice',
            'value' => '001714889000074',
        ]);
        Setting::create([
            'key' => 'email',
            'value' => 'monabous@hotmail.com',
        ]);
        Setting::create([
            'key' => 'phone',
            'value' => '0611345339',
        ]);
        Setting::create([
            'key' => 'waiting',
            'value' => 0,
        ]);
    }
}
