<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['name' => 'Pessoas', 'slug' => 'people', 'order' => 1, 'status' => true],
            ['name' => 'Tenants', 'slug' => 'tenants', 'order' => 2, 'status' => true],
            ['name' => 'Usuários', 'slug' => 'users', 'order' => 3, 'status' => true],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }
    }
}
