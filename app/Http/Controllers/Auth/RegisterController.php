<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'slug'         => 'required|string|max:63|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            'email'        => 'required|email|max:255',
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
            'email.required'        => 'O email é obrigatório.',
            'email.email'           => 'O email deve ser válido.',
            'password.required'     => 'A senha é obrigatória.',
            'password.min'          => 'A senha deve ter no mínimo 8 caracteres.',
            'first_name.required'   => 'O nome é obrigatório.',
            'surname.required'      => 'O sobrenome é obrigatório.',
            'whatsapp.required'     => 'O WhatsApp é obrigatório.',
            'cpf_cnpj.required'     => 'O CPF/CNPJ é obrigatório.',
            'plan.required'         => 'Selecione um plano.',
            'billing_cycle.required'=> 'Selecione o ciclo de cobrança.',
        ]);

        // Por enquanto, apenas exibe os dados para debug
        // Na Fase 4 será implementada a gravação no banco
        dd($validated);
    }
}
