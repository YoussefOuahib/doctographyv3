<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/*models */
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $doctor = User::factory()->create();
        $doctor->roles()->attach(Role::where('name', 'Doctor')->value('id'));

        $receptionist = User::factory()->create();
        $receptionist->roles()->attach(Role::where('name', 'Receptionist')->value('id'));
    }
}
