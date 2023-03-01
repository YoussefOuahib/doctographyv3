<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $doctor = Role::create(['name' => 'Doctor']);
        $doctor->permissions()->attach(Permission::whereIn('name', [
            'patients.delete',
            'patients.update',
            'appointments.store',
            'appointments.delete',
            'appointments.update',
            'conditions.manage',
            'settings.manage',
            'invoice.generate',
            'prescription.generate',
            'waiting.refresh',
            'home.view'
        ])->pluck('id'));

        $receptionist = Role::create(['name' => 'Receptionist']);
        $receptionist->permissions()->attach(Permission::whereIn('name', [
            'patients.update',
            'upcomings.store',
            'appointments.delete',
            'waiting.increment',
            'waiting.decrement',

            
        ])->pluck('id'));
    }
}
