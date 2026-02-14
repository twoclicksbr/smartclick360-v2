<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\File;
use App\Models\Tenant\Module;
use App\Models\Tenant\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Lógica específica para people com first_name + surname
     */
    public function index(Request $request, string $slug, string $module)
    {
        return view('tenant.pages.people.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug, string $module)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Validação
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // max 2MB
        ], [
            'first_name.required' => 'O nome é obrigatório',
            'surname.required' => 'O sobrenome é obrigatório',
            'birth_date.date' => 'A data de nascimento deve ser uma data válida',
            'avatar.image' => 'O arquivo deve ser uma imagem',
            'avatar.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg',
            'avatar.max' => 'A imagem não pode ser maior que 2MB',
        ]);

        // Cria a pessoa
        $person = Person::create([
            'first_name' => $validated['first_name'],
            'surname' => $validated['surname'],
            'birth_date' => $validated['birth_date'] ?? null,
            'order' => Person::max('order') + 1, // próximo na ordem
            'status' => $request->input('status', 1) == 1, // pega do formulário, padrão ativo
        ]);

        // Processa o upload do avatar (se houver)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            // Define o caminho de armazenamento: storage/app/public/tenants/{slug}/avatars/
            $path = $file->store('tenants/' . $tenant->slug . '/avatars', 'public');

            // Busca o module_id de 'people'
            $moduleId = Module::where('slug', 'people')->value('id');

            // Cria o registro na tabela files (polimórfico)
            File::create([
                'module_id' => $moduleId,
                'register_id' => $person->id,
                'name' => 'avatar',
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'order' => 0,
                'status' => true,
            ]);
        }

        return redirect('/people/' . encodeId($person->id))
            ->with('success', 'Pessoa adicionada com sucesso!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Decodifica o código para obter o ID
        $id = decodeId($code);

        // Busca a pessoa
        $person = Person::findOrFail($id);

        // Validação
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'first_name.required' => 'O nome é obrigatório',
            'surname.required' => 'O sobrenome é obrigatório',
            'birth_date.date' => 'A data de nascimento deve ser uma data válida',
            'avatar.image' => 'O arquivo deve ser uma imagem',
            'avatar.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg',
            'avatar.max' => 'A imagem não pode ser maior que 2MB',
        ]);

        // Atualiza a pessoa
        $person->update([
            'first_name' => $validated['first_name'],
            'surname' => $validated['surname'],
            'birth_date' => $validated['birth_date'] ?? null,
            'status' => $request->input('status', 1) == 1, // pega do formulário, padrão ativo
        ]);

        // Processa o upload do avatar (se houver)
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            // Define o caminho de armazenamento
            $path = $file->store('tenants/' . $tenant->slug . '/avatars', 'public');

            // Busca o module_id de 'people'
            $moduleId = Module::where('slug', 'people')->value('id');

            // Remove avatar antigo se existir
            $oldAvatar = File::where('module_id', $moduleId)
                ->where('register_id', $person->id)
                ->where('name', 'avatar')
                ->first();

            if ($oldAvatar) {
                // Remove arquivo físico
                Storage::disk('public')->delete($oldAvatar->path);
                // Remove registro do banco
                $oldAvatar->delete();
            }

            // Cria novo registro
            File::create([
                'module_id' => $moduleId,
                'register_id' => $person->id,
                'name' => 'avatar',
                'path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'order' => 0,
                'status' => true,
            ]);
        }

        return redirect('/people/' . encodeId($person->id))
            ->with('success', 'Pessoa atualizada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug, string $module, string $code)
    {
        return view('tenant.pages.people.show', compact('code'));
    }

    /**
     * Exibe a página de arquivos da pessoa
     */
    public function showFiles(string $slug, string $code)
    {
        return view('tenant.pages.people.show-files', compact('code'));
    }
}
