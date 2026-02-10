<?php

namespace Database\Seeders;

use App\Models\Landlord\Plan;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Plano ideal para pequenas empresas que estão começando',
                'price_monthly' => 99.90,
                'price_yearly' => 999.00,
                'features' => [
                    'CRM Básico',
                    'Gestão Financeira',
                    '5 Usuários',
                    'Suporte por Email',
                ],
                'max_users' => 5,
                'order' => 0,
                'status' => true,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Plano completo para empresas em crescimento',
                'price_monthly' => 199.90,
                'price_yearly' => 1999.00,
                'features' => [
                    'CRM Completo',
                    'Gestão Financeira Avançada',
                    'Gestão de Estoque',
                    '20 Usuários',
                    'Suporte Prioritário',
                    'Relatórios Personalizados',
                ],
                'max_users' => 20,
                'order' => 1,
                'status' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Solução empresarial com recursos ilimitados',
                'price_monthly' => 499.90,
                'price_yearly' => 4999.00,
                'features' => [
                    'Todos os Módulos',
                    'Usuários Ilimitados',
                    'Suporte 24/7',
                    'Relatórios Customizados',
                    'API Completa',
                    'Integrações Personalizadas',
                    'Gerente de Conta Dedicado',
                ],
                'max_users' => 999,
                'order' => 2,
                'status' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
