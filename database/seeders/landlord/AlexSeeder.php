<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Person;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\User;
use Illuminate\Database\Seeder;

class AlexSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar tenant SmartClick360
        $tenant = Tenant::create([
            'name'          => 'SmartClick360',
            'slug'          => 'smartclick360',
            'database_name' => 'sc360_main',
            'order'         => 1,
            'status'        => 'active',
        ]);

        // 2. Criar person
        $person = Person::create([
            'tenant_id'  => $tenant->id,
            'first_name' => 'Alex',
            'surname'    => 'Alves de Almeida',
            'birth_date' => '1990-05-15',
            'order'      => 1,
        ]);

        // 3. Criar user
        User::create([
            'person_id' => $person->id,
            'email'     => 'alex@smartclick360.com',
            'password'  => 'Millena2012@',
            'order'     => 1,
        ]);

        // Contacts e documents foram removidos porque dependem do módulo 'people'
        // que não existe mais no banco (seeders de modules foram esvaziados)
    }
}
