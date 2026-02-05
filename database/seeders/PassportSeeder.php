<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar Personal Access Client
        $clientId = (string) Str::uuid();
        DB::connection('tenant')->table('oauth_clients')->insert([
            'id' => $clientId,
            'owner_type' => null,
            'owner_id' => null,
            'name' => config('app.name') . ' Personal Access Client',
            'secret' => hash('sha256', Str::random(40)),
            'provider' => 'users',
            'redirect_uris' => json_encode([]),
            'grant_types' => json_encode(['personal_access']),
            'revoked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar Password Grant Client
        $passwordClientId = (string) Str::uuid();
        DB::connection('tenant')->table('oauth_clients')->insert([
            'id' => $passwordClientId,
            'owner_type' => null,
            'owner_id' => null,
            'name' => config('app.name') . ' Password Grant Client',
            'secret' => hash('sha256', Str::random(40)),
            'provider' => 'users',
            'redirect_uris' => json_encode([]),
            'grant_types' => json_encode(['password']),
            'revoked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✓ Personal Access Client criado (ID: {$clientId})\n";
        echo "✓ Password Grant Client criado (ID: {$passwordClientId})\n";
    }
}
