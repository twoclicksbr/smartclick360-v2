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
        // dd('index específico de PeopleController');

        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Inicia a query com eager loading de WhatsApp e Avatar
        $query = Person::with([
            'contacts' => function ($query) {
                $query->whereHas('typeContact', function ($q) {
                    $q->where('name', 'WhatsApp');
                })->with('typeContact');
            },
            'files' => function ($query) {
                $query->where('name', 'avatar');
            }
        ]);

        // ==============================================
        // FILTROS DE PESQUISA AVANÇADA
        // ==============================================

        // Filtro: Busca Rápida (campo do header)
        if ($request->filled('quick_search')) {
            $quickSearch = $request->quick_search;
            $query->where(function ($q) use ($quickSearch) {
                // Busca no nome completo (first_name + surname)
                $q->whereRaw("first_name || ' ' || surname ILIKE ?", ['%' . $quickSearch . '%'])
                    // Ou busca no ID
                    ->orWhere('id', 'LIKE', '%' . $quickSearch . '%');
            });
        }

        // Filtro: ID
        if ($request->filled('search_id')) {
            $query->where('id', $request->search_id);
        }

        // Filtro: Nome (busca na concatenação de first_name + surname)
        if ($request->filled('search_name')) {
            $searchName = $request->search_name;
            $operator = $request->search_operator ?? 'contains';

            $query->where(function ($q) use ($searchName, $operator) {
                if ($operator === 'contains') {
                    // Busca na concatenação de first_name + surname (case-insensitive)
                    $q->whereRaw("first_name || ' ' || surname ILIKE ?", ['%' . $searchName . '%']);
                } elseif ($operator === 'starts_with') {
                    // Busca se o nome completo começa com o termo
                    $q->whereRaw("first_name || ' ' || surname ILIKE ?", [$searchName . '%']);
                } elseif ($operator === 'exact') {
                    // Busca exata no nome completo
                    $q->whereRaw("first_name || ' ' || surname ILIKE ?", [$searchName]);
                }
            });
        }

        // Filtro: Status
        if ($request->filled('search_status')) {
            $query->where('status', $request->search_status);
        }

        // Filtro: Incluir deletados
        if ($request->filled('search_deleted') && $request->search_deleted == '1') {
            $query->withTrashed();
        }

        // Filtro: Datas (periodo com daterangepicker)
        if ($request->filled('search_date_range')) {
            // Parse do range "DD/MM/YYYY - DD/MM/YYYY"
            $dates = explode(' - ', $request->search_date_range);
            if (count($dates) === 2) {
                try {
                    $startDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                    $dateField = $request->search_date_field ?? 'created_at';

                    $query->whereBetween($dateField, [$startDate, $endDate]);
                } catch (\Exception $e) {
                    // Se houver erro no parse da data, ignora o filtro
                }
            }
        }

        // Ordenação e paginação
        $perPage = $request->search_per_page ?? 25;

        // Ordenação dinâmica (se houver parâmetro sort_by)
        $sortBy = $request->get('sort_by', 'order'); // padrão: order
        $sortDirection = $request->get('sort_direction', 'asc'); // padrão: asc

        // Validação: apenas colunas permitidas
        $allowedColumns = ['id', 'first_name', 'status', 'order', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'order';
        }

        // Validação: apenas asc ou desc
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $people = $query
            ->orderBy($sortBy, $sortDirection)
            ->orderBy('first_name', 'asc') // fallback para nome quando ordem for igual
            ->paginate($perPage)
            ->appends($request->except('page')); // mantém os filtros na paginação

        // Se for requisição AJAX, retorna apenas a tabela (HTML parcial)
        if ($request->ajax()) {
            return view('tenant.components.people-table', [
                'people' => $people,
            ])->render();
        }

        // Se for requisição normal, retorna a view completa
        return view('tenant.pages.people.index', [
            'people' => $people,
            'tenant' => $tenant,
        ]);
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
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Decodifica o código para obter o ID
        $id = decodeId($code);

        // Busca a pessoa com relacionamentos necessários (files foi movido para página separada)
        $person = Person::with([
            'contacts.typeContact',
            'documents.typeDocument',
            'addresses.typeAddress',
            'notes'
        ])->findOrFail($id);

        return view('tenant.pages.people.show', [
            'person' => $person,
            'tenant' => $tenant,
            'module' => $module,
        ]);
    }

    /**
     * Exibe a página de arquivos da pessoa
     */
    public function showFiles(string $slug, string $code)
    {
        $tenant = request()->attributes->get('tenant');

        // Decodifica o código para obter o ID
        $id = decodeId($code);

        // Busca a pessoa com arquivos e contatos (para exibir no header)
        $person = Person::with(['files', 'contacts.typeContact'])->findOrFail($id);

        return view('tenant.pages.people.show-files', [
            'person' => $person,
            'tenant' => $tenant,
            'module' => 'people',
        ]);
    }
}
