<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function showForm()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'slug'         => 'required|string|max:63|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:tenants,slug',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|string|min:8',
            'first_name'   => 'required|string|max:255',
            'surname'      => 'required|string|max:255',
            'whatsapp'     => 'required|string|max:20',
            'cpf_cnpj'     => 'required|string|max:18',
            'plan'         => 'required|in:starter,professional,enterprise',
            'billing_cycle'=> 'required|in:monthly,yearly',
        ], [
            'company_name.required' => 'O nome da empresa é obrigatório.',
            'slug.required'         => 'O slug é obrigatório.',
            'slug.regex'            => 'O slug contém caracteres inválidos.',
            'slug.unique'           => 'Este slug já está em uso.',
            'email.required'        => 'O email é obrigatório.',
            'email.email'           => 'O email deve ser válido.',
            'email.unique'          => 'Este email já está cadastrado.',
            'password.required'     => 'A senha é obrigatória.',
            'password.min'          => 'A senha deve ter no mínimo 8 caracteres.',
            'first_name.required'   => 'O nome é obrigatório.',
            'surname.required'      => 'O sobrenome é obrigatório.',
            'whatsapp.required'     => 'O WhatsApp é obrigatório.',
            'cpf_cnpj.required'     => 'O CPF/CNPJ é obrigatório.',
            'plan.required'         => 'Selecione um plano.',
            'billing_cycle.required'=> 'Selecione o ciclo de cobrança.',
        ]);

        try {
            // Create tenant and all related data
            $tenant = $this->tenantService->create($validated);

            // Redirect to login with success message
            return redirect()->route('login')->with('success',
                'Conta criada com sucesso! Você tem 15 dias de trial gratuito. Faça login para começar.'
            );
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Tenant creation failed: ' . $e->getMessage());

            // Redirect back with error
            return back()->withInput()->withErrors([
                'error' => 'Ocorreu um erro ao criar sua conta. Por favor, tente novamente.'
            ]);
        }
    }
}
