<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Módulos principais
        $modules = [
            ['slug' => 'persons', 'name' => 'Persons', 'icon' => 'users', 'order' => 1, 'type' => 'module'],
            ['slug' => 'products', 'name' => 'Products', 'icon' => 'package', 'order' => 2, 'type' => 'module'],
            ['slug' => 'sales', 'name' => 'Sales', 'icon' => 'shopping-cart', 'order' => 3, 'type' => 'module'],
        ];

        foreach ($modules as $moduleData) {
            Module::updateOrCreate(
                ['slug' => $moduleData['slug']],
                $moduleData
            );
        }

        // Submódulos
        $submodules = [
            ['slug' => 'files', 'name' => 'Files', 'icon' => 'file', 'order' => 1, 'type' => 'submodule'],
            ['slug' => 'notes', 'name' => 'Notes', 'icon' => 'sticky-note', 'order' => 2, 'type' => 'submodule'],
            ['slug' => 'addresses', 'name' => 'Addresses', 'icon' => 'map-marker', 'order' => 3, 'type' => 'submodule'],
            ['slug' => 'contacts', 'name' => 'Contacts', 'icon' => 'phone', 'order' => 4, 'type' => 'submodule'],
            ['slug' => 'documents', 'name' => 'Documents', 'icon' => 'id-card', 'order' => 5, 'type' => 'submodule'],
        ];

        foreach ($submodules as $submoduleData) {
            Module::updateOrCreate(
                ['slug' => $submoduleData['slug']],
                $submoduleData
            );
        }

        $this->command->info('Módulos e submódulos criados com sucesso!');

        // Vincular submódulos aos módulos (conforme página 8 da especificação)
        $personsModule = Module::where('slug', 'persons')->first();
        $productsModule = Module::where('slug', 'products')->first();

        if ($personsModule) {
            $personsModule->submodules()->syncWithoutDetaching([
                Module::where('slug', 'files')->first()->id => ['order' => 1, 'active' => true],
                Module::where('slug', 'notes')->first()->id => ['order' => 2, 'active' => true],
                Module::where('slug', 'addresses')->first()->id => ['order' => 3, 'active' => true],
                Module::where('slug', 'contacts')->first()->id => ['order' => 4, 'active' => true],
                Module::where('slug', 'documents')->first()->id => ['order' => 5, 'active' => true],
            ]);
            $this->command->info('Submódulos vinculados ao módulo Persons!');
        }

        if ($productsModule) {
            $productsModule->submodules()->syncWithoutDetaching([
                Module::where('slug', 'files')->first()->id => ['order' => 1, 'active' => true],
                Module::where('slug', 'notes')->first()->id => ['order' => 2, 'active' => true],
            ]);
            $this->command->info('Submódulos vinculados ao módulo Products!');
        }
    }
}
