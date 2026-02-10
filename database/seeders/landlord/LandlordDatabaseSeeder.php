<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;

class LandlordDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleSeeder::class,
            TypeContactSeeder::class,
            TypeDocumentSeeder::class,
            TypeAddressSeeder::class,
            PlanSeeder::class,
        ]);
    }
}
