<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Permission;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'patients.store']);
        Permission::create(['name' => 'patients.update']);
        Permission::create(['name' => 'patients.delete']);
        Permission::create(['name' => 'appointments.store']);
        Permission::create(['name' => 'appointments.update']);
        Permission::create(['name' => 'appointments.delete']);
        Permission::create(['name' => 'appointments.change_date']);
        Permission::create(['name' => 'conditions.manage']);
        Permission::create(['name' => 'settings.manage']);
        Permission::create(['name' => 'home.view']);
        Permission::create(['name' => 'upcomings.store']);
        Permission::create(['name' => 'invoice.generate']);
        Permission::create(['name' => 'prescription.generate']);
        Permission::create(['name' => 'waiting.refresh']);
        Permission::create(['name' => 'waiting.increment']);
        Permission::create(['name' => 'waiting.decrement']);





        




    }
}
