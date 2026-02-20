<?php

namespace Database\Seeders\Landlord;

use Illuminate\Database\Seeder;

class LandlordDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleSeeder::class,
            ModuleSubmoduleSeeder::class,
            ModuleFieldSeeder::class,
            ModuleFieldUiSeeder::class,
            TypeContactSeeder::class,
            TypeDocumentSeeder::class,
            TypeAddressSeeder::class,
            TypeProductSeeder::class,
            UnitSeeder::class,
            OriginSeeder::class,
            CfopSeeder::class,
            TaxSituationSeeder::class,
            TransactionSeeder::class,
            PlanSeeder::class,
            ModuleManagerSeeder::class,
            AlexSeeder::class,
        ]);
    }
}
