<?php

namespace Database\Seeders\Landlord;

use App\Models\Landlord\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Starter',
                'slug'          => 'starter',
                'description'   => 'Ideal para pequenos negócios',
                'price_monthly' => 97.00,
                'price_yearly'  => 970.00,
                'max_users'     => 3,
                'features'      => json_encode([
                    'max_users' => 3,
                    'modules' => ['Pessoas', 'Vendas'],
                    'priority_support' => false
                ]),
                'order'         => 1,
            ],
            [
                'name'          => 'Professional',
                'slug'          => 'professional',
                'description'   => 'Para empresas em crescimento',
                'price_monthly' => 197.00,
                'price_yearly'  => 1970.00,
                'max_users'     => 10,
                'features'      => json_encode([
                    'max_users' => 10,
                    'modules' => ['all'],
                    'priority_support' => true
                ]),
                'order'         => 2,
            ],
            [
                'name'          => 'Enterprise',
                'slug'          => 'enterprise',
                'description'   => 'Para grandes operações',
                'price_monthly' => 397.00,
                'price_yearly'  => 3970.00,
                'max_users'     => 50,
                'features'      => json_encode([
                    'max_users' => 50,
                    'modules' => ['all'],
                    'priority_support' => true,
                    'dedicated_support' => true,
                    'api_access' => true
                ]),
                'order'         => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
