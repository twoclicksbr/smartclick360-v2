<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function checkSlug(Request $request)
    {
        $request->validate(['slug' => 'required|string|max:63']);

        $exists = \App\Models\Landlord\Tenant::where('slug', $request->slug)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Este subdomínio já está em uso.' : 'Subdomínio disponível!',
        ]);
    }

    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        $exists = \App\Models\Landlord\User::where('email', $request->email)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Este email já está em uso.' : 'Email disponível!',
        ]);
    }

    public function store(Request $request)
    {
        // Criar validador manual
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'surname'       => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'phone'         => 'required|string|max:20',
            'document'      => 'required|string|max:18',
            'company_name'  => 'required|string|max:255',
            'slug'          => 'required|string|max:63|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/|unique:tenants,slug',
            'password'      => 'required|string|min:8|confirmed',
            'plan'          => 'required|in:starter,professional,enterprise',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        // Se houver erros, retorna com os erros
        if ($validator->fails()) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors($validator);
        }

        $validated = $validator->validated();

        // Remover máscaras dos campos antes de enviar para o service
        $validated['phone'] = preg_replace('/\D/', '', $validated['phone']);
        $validated['document'] = preg_replace('/\D/', '', $validated['document']);

        try {
            $tenantService = new TenantService();
            $tenant = $tenantService->createTenant($validated);

            return redirect("http://{$tenant->slug}.smartclick360-v2.test/login")
                ->with('success', 'Conta criada com sucesso! Faça login para continuar.');

        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Erro ao criar conta: ' . $e->getMessage());
        }
    }
}
