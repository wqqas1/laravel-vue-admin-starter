<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['title' => 'SuperAdmin'],
            ['title' => 'Admin'],
        ];

        Role::insert($roles);
    }
}
