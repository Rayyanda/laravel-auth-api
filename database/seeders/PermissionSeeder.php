<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $permissions = ['create role', 'edit role', 'delete role', 'view role'];
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }
    }
}
