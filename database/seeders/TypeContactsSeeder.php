<?php

namespace Database\Seeders;

use App\Models\Landlord\TypeContact;
use Illuminate\Database\Seeder;

class TypeContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Email', 'slug' => 'email', 'icon' => 'ki-sms', 'is_active' => true],
            ['name' => 'Telefone', 'slug' => 'phone', 'icon' => 'ki-phone', 'is_active' => true],
            ['name' => 'WhatsApp', 'slug' => 'whatsapp', 'icon' => 'ki-whatsapp', 'is_active' => true],
            ['name' => 'Celular', 'slug' => 'mobile', 'icon' => 'ki-device', 'is_active' => true],
        ];

        foreach ($types as $type) {
            TypeContact::create($type);
        }
    }
}
