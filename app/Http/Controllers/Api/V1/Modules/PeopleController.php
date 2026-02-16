<?php

namespace App\Http\Controllers\Api\V1\Modules;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Tenant\File;
use App\Models\Tenant\Module;
use App\Models\Tenant\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PeopleController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
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
            $ids = $this->parseIdFilter($request->search_id);
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
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

        return $this->success([
            'people' => $people,
            'filters' => $request->only([
                'quick_search',
                'search_id',
                'search_name',
                'search_operator',
                'search_status',
                'search_deleted',
                'search_date_range',
                'search_date_field',
                'search_per_page',
                'sort_by',
                'sort_direction',
            ]),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
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

        // Recarrega a pessoa com relacionamentos
        $person->load(['contacts.typeContact', 'files']);

        return $this->created($person, 'Pessoa adicionada com sucesso!');
    }

    public function show(Request $request, string $code): JsonResponse
    {
        // Decodifica o código para obter o ID
        $id = decodeId($code);

        // Busca a pessoa com relacionamentos necessários (incluindo soft-deleted)
        $person = Person::withTrashed()->with([
            'contacts.typeContact',
            'documents.typeDocument',
            'addresses' => function($query) {
                $query->orderBy('is_main', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->with('typeAddress');
            },
            'notes',
            'files'
        ])->findOrFail($id);

        return $this->success([
            'person' => $person,
        ]);
    }

    public function update(Request $request, string $code): JsonResponse
    {
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

        // Recarrega a pessoa com relacionamentos
        $person->load(['contacts.typeContact', 'files']);

        return $this->success($person, 'Pessoa atualizada com sucesso!');
    }

    public function destroy(Request $request, string $code): JsonResponse
    {
        // Decodifica o código para obter o ID
        $id = decodeId($code);

        // Busca a pessoa
        $person = Person::findOrFail($id);

        // Soft delete
        $person->delete();

        return $this->deleted('Pessoa removida com sucesso');
    }

    public function restore(Request $request, string $code): JsonResponse
    {
        // Decodifica o código para obter o ID
        $id = decodeId($code);

        // Busca a pessoa (incluindo deletados)
        $person = Person::withTrashed()->findOrFail($id);

        // Restaura
        $person->restore();

        return $this->restored('Pessoa restaurada com sucesso');
    }

    /**
     * Parseia string de IDs flexível
     * Suporta: "2" | "2,5" | "1-5" | "2,4-8,11"
     */
    private function parseIdFilter(string $input): array
    {
        $ids = [];
        $parts = explode(',', $input);

        foreach ($parts as $part) {
            $part = trim($part);
            if (str_contains($part, '-')) {
                // Range: "1-5" → [1,2,3,4,5]
                [$start, $end] = explode('-', $part, 2);
                $start = (int) trim($start);
                $end = (int) trim($end);
                if ($start > 0 && $end > 0 && $end >= $start) {
                    $ids = array_merge($ids, range($start, $end));
                }
            } else {
                // Single: "2" → [2]
                $id = (int) $part;
                if ($id > 0) {
                    $ids[] = $id;
                }
            }
        }

        return array_unique($ids);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer',
        ]);

        foreach ($request->order as $id => $order) {
            Person::where('id', $id)->update(['order' => $order]);
        }

        return $this->success(null, 'Ordem atualizada com sucesso');
    }
}
