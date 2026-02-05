<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Person;
use App\Models\PersonPermission;
use App\Models\PersonType;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar status padrão (ativo)
        $activeStatus = Status::where('slug', 'active')->first();

        // Buscar tipo 'employee'
        $employeeType = PersonType::where('slug', 'employee')->first();

        // Criar pessoa admin
        $person = Person::firstOrCreate(
            ['name' => 'Admin Demo'],
            [
                'birthdate' => null,
                'order' => 0,
                'status_id' => $activeStatus?->id,
            ]
        );

        // Sincronizar tipo de pessoa (employee)
        if ($employeeType) {
            $person->personTypes()->syncWithoutDetaching([$employeeType->id]);
        }

        $this->command->info("Person '{$person->name}' criada!");

        // Criar usuário admin
        $user = User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'person_id' => $person->id,
                'password' => Hash::make('password'),
            ]
        );

        $this->command->info("User '{$user->email}' criado! Senha: password");

        // Dar todas as permissões para o admin
        $modules = Module::all();
        $actions = ['view', 'create', 'update', 'delete', 'export'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                PersonPermission::updateOrCreate(
                    [
                        'person_id' => $person->id,
                        'module_id' => $module->id,
                        'action' => $action,
                    ]
                );
            }
        }

        $this->command->info("Permissões criadas para {$person->name}!");
    }
}
