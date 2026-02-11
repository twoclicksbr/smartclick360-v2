<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Contact;
use App\Models\Landlord\Document;
use App\Models\Landlord\Module;
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
            'order'      => 1,
        ]);

        // 3. Criar user
        User::create([
            'person_id' => $person->id,
            'email'     => 'alex@smartclick360.com',
            'password'  => 'Millena2012@',
            'order'     => 1,
        ]);

        // 4. Buscar IDs de referência
        $modulePeople   = Module::where('slug', 'people')->first();
        $typeWhatsApp   = \App\Models\Landlord\TypeContact::where('name', 'WhatsApp')->first();
        $typeEmail      = \App\Models\Landlord\TypeContact::where('name', 'Email')->first();
        $typeCPF        = \App\Models\Landlord\TypeDocument::where('name', 'CPF')->first();

        // 5. Criar contact WhatsApp
        Contact::create([
            'type_contact_id' => $typeWhatsApp->id,
            'module_id'       => $modulePeople->id,
            'register_id'     => $person->id,
            'value'           => '12997698040',
            'order'           => 1,
        ]);

        // 6. Criar contact Email
        Contact::create([
            'type_contact_id' => $typeEmail->id,
            'module_id'       => $modulePeople->id,
            'register_id'     => $person->id,
            'value'           => 'alex@smartclick360.com',
            'order'           => 2,
        ]);

        // 7. Criar document CPF
        Document::create([
            'type_document_id' => $typeCPF->id,
            'module_id'        => $modulePeople->id,
            'register_id'      => $person->id,
            'value'            => '35564485807',
            'order'            => 1,
        ]);
    }
}
