<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PeopleFakeSeeder extends Seeder
{
    public function run(): void
    {
        // Configura a conexão para o tenant
        $connection = 'tenant';

        // Usa Faker com locale pt_BR
        $faker = Faker::create('pt_BR');

        // Busca o módulo "people" para vincular contatos
        $peopleModuleId = DB::connection($connection)
            ->table('modules')
            ->where('slug', 'people')
            ->value('id');

        // Busca o tipo de contato "WhatsApp"
        $whatsappTypeId = DB::connection($connection)
            ->table('type_contacts')
            ->where('name', 'WhatsApp')
            ->value('id');

        // Conta quantas pessoas já existem
        $existingCount = DB::connection($connection)
            ->table('people')
            ->count();

        $recordsToCreate = 50 - $existingCount;

        if ($recordsToCreate <= 0) {
            $this->command->info("✓ Já existem $existingCount pessoas cadastradas.");
            return;
        }

        // Nomes brasileiros comuns
        $firstNames = [
            'João', 'Maria', 'José', 'Ana', 'Paulo', 'Beatriz', 'Carlos', 'Juliana',
            'Pedro', 'Fernanda', 'Lucas', 'Mariana', 'Rafael', 'Camila', 'Felipe',
            'Larissa', 'Bruno', 'Amanda', 'Rodrigo', 'Gabriela', 'Gustavo', 'Isabela',
            'Thiago', 'Letícia', 'Marcelo', 'Patricia', 'André', 'Renata', 'Diego',
            'Vanessa', 'Ricardo', 'Tatiana', 'Leandro', 'Bruna', 'Fábio', 'Carla',
            'Vinícius', 'Priscila', 'Alexandre', 'Mônica', 'Leonardo', 'Claudia',
            'Matheus', 'Alessandra', 'Daniel', 'Simone', 'Guilherme', 'Roberta'
        ];

        $surnames = [
            'Silva', 'Santos', 'Oliveira', 'Souza', 'Rodrigues', 'Ferreira', 'Alves',
            'Pereira', 'Lima', 'Gomes', 'Costa', 'Ribeiro', 'Martins', 'Carvalho',
            'Almeida', 'Lopes', 'Soares', 'Fernandes', 'Vieira', 'Barbosa', 'Rocha',
            'Dias', 'Nascimento', 'Castro', 'Araújo', 'Cardoso', 'Correia', 'Teixeira',
            'Mendes', 'Pinto', 'Moreira', 'Monteiro', 'Ramos', 'Nunes', 'Freitas',
            'Machado', 'Cavalcanti', 'Melo', 'Campos', 'Miranda', 'Azevedo', 'Cunha'
        ];

        $this->command->info("Gerando $recordsToCreate registros fake...");

        for ($i = 0; $i < $recordsToCreate; $i++) {
            // Busca o maior order atual
            $maxOrder = DB::connection($connection)
                ->table('people')
                ->max('order') ?? 0;

            $firstName = $firstNames[array_rand($firstNames)];
            $surname = $surnames[array_rand($surnames)];
            $status = $faker->boolean(80); // 80% ativo

            // Insere pessoa
            $personId = DB::connection($connection)
                ->table('people')
                ->insertGetId([
                    'first_name' => $firstName,
                    'surname' => $surname,
                    'order' => $maxOrder + 1,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            // 70% de chance de ter WhatsApp
            if ($faker->boolean(70) && $whatsappTypeId) {
                $ddd = $faker->numberBetween(11, 99);
                $number = '9' . $faker->numerify('########'); // 9XXXXXXXX

                DB::connection($connection)
                    ->table('contacts')
                    ->insert([
                        'type_contact_id' => $whatsappTypeId,
                        'module_id' => $peopleModuleId,
                        'register_id' => $personId,
                        'value' => $ddd . $number, // Apenas números
                        'order' => 1,
                        'status' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            if (($i + 1) % 10 === 0) {
                $current = $i + 1;
                $this->command->info("✓ $current/$recordsToCreate registros criados");
            }
        }

        $total = DB::connection($connection)
            ->table('people')
            ->count();

        $this->command->info("✓ Concluído! Total de pessoas: $total");
    }
}
