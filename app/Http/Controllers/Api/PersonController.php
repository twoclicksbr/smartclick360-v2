<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = min($request->get('per_page', 15), 100); // Máximo 100 registros por página

        $query = Person::query()->with(['personTypes', 'status']);

        // Filtro de busca geral (múltiplos campos)
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Filtros individuais
        if ($name = $request->get('name')) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($statusId = $request->get('status_id')) {
            $query->where('status_id', $statusId);
        }

        if ($personTypeId = $request->get('person_type_id')) {
            $query->whereHas('personTypes', function ($q) use ($personTypeId) {
                $q->where('person_types.id', $personTypeId);
            });
        }

        if ($birthdate = $request->get('birthdate')) {
            $query->whereDate('birthdate', $birthdate);
        }

        // Ordenação
        $orderBy = $request->get('order_by', 'order');
        $orderDirection = $request->get('order_direction', 'asc');

        if (in_array($orderBy, ['name', 'birthdate', 'order', 'created_at'])) {
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->orderBy('order', 'asc')->orderBy('name', 'asc');
        }

        $persons = $query->paginate($perPage);

        // Transformar cada person para incluir permissões formatadas
        $data = collect($persons->items())->map(function ($person) {
            $personArray = $person->toArray();
            $personArray['permissions'] = $person->formattedPermissions();
            return $personArray;
        })->all();

        return response()->json([
            'status' => 200,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $persons->currentPage(),
                'per_page' => $persons->perPage(),
                'total' => $persons->total(),
                'last_page' => $persons->lastPage(),
                'from' => $persons->firstItem(),
                'to' => $persons->lastItem(),
            ],
            'links' => [
                'first' => $persons->url(1),
                'last' => $persons->url($persons->lastPage()),
                'prev' => $persons->previousPageUrl(),
                'next' => $persons->nextPageUrl(),
            ],
            'filters' => [
                'available' => [
                    'name' => [
                        'field' => 'name',
                        'type' => 'string',
                        'operator' => 'like',
                        'description' => 'Filtrar por nome',
                        'example' => '?name=João'
                    ],
                    'status_id' => [
                        'field' => 'status_id',
                        'type' => 'integer',
                        'operator' => '=',
                        'description' => 'Filtrar por status',
                        'example' => '?status_id=1'
                    ],
                    'person_type_id' => [
                        'field' => 'person_type_id',
                        'type' => 'integer',
                        'operator' => 'has',
                        'description' => 'Filtrar por tipo de pessoa',
                        'example' => '?person_type_id=1'
                    ],
                    'birthdate' => [
                        'field' => 'birthdate',
                        'type' => 'date',
                        'operator' => '=',
                        'description' => 'Filtrar por data de nascimento',
                        'example' => '?birthdate=1990-01-01'
                    ],
                    'search' => [
                        'field' => 'multiple',
                        'type' => 'string',
                        'operator' => 'like',
                        'searchable_fields' => ['name'],
                        'description' => 'Busca global por nome',
                        'example' => '?search=João'
                    ],
                    'order_by' => [
                        'field' => 'sorting',
                        'type' => 'string',
                        'operator' => null,
                        'values' => ['name', 'birthdate', 'order', 'created_at'],
                        'description' => 'Campo para ordenação',
                        'default' => 'order',
                        'example' => '?order_by=name'
                    ],
                    'order_direction' => [
                        'field' => 'sorting',
                        'type' => 'string',
                        'operator' => null,
                        'values' => ['asc', 'desc'],
                        'description' => 'Direção da ordenação',
                        'default' => 'asc',
                        'example' => '?order_direction=desc'
                    ],
                    'per_page' => [
                        'field' => 'pagination',
                        'type' => 'integer',
                        'operator' => null,
                        'description' => 'Registros por página (max: 100)',
                        'default' => 15,
                        'example' => '?per_page=20'
                    ]
                ],
                'active' => array_filter([
                    'search' => $request->get('search'),
                    'name' => $request->get('name'),
                    'status_id' => $request->get('status_id'),
                    'person_type_id' => $request->get('person_type_id'),
                    'birthdate' => $request->get('birthdate'),
                    'order_by' => $request->get('order_by'),
                    'order_direction' => $request->get('order_direction'),
                    'per_page' => $request->get('per_page'),
                ])
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birthdate' => 'nullable|date',
            'order' => 'nullable|integer|min:0',
            'status_id' => 'nullable|exists:tenant.statuses,id',
            'person_type_ids' => 'nullable|array',
            'person_type_ids.*' => 'exists:tenant.person_types,id',
        ]);

        $personTypeIds = $validated['person_type_ids'] ?? [];
        unset($validated['person_type_ids']);

        $person = Person::create($validated);

        // Sincronizar tipos de pessoa (many-to-many)
        if (!empty($personTypeIds)) {
            $person->personTypes()->sync($personTypeIds);
        }

        return response()->json([
            'status' => 201,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'data' => $person->load(['personTypes', 'status']),
            'message' => 'Registro criado com sucesso',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Person $person)
    {
        $person->load(['users', 'personTypes', 'status']);

        $data = $person->toArray();
        $data['permissions'] = $person->formattedPermissions();

        return response()->json([
            'status' => 200,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Person $person)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'birthdate' => 'nullable|date',
            'order' => 'nullable|integer|min:0',
            'status_id' => 'nullable|exists:tenant.statuses,id',
            'person_type_ids' => 'nullable|array',
            'person_type_ids.*' => 'exists:tenant.person_types,id',
        ]);

        $personTypeIds = $validated['person_type_ids'] ?? null;
        unset($validated['person_type_ids']);

        $person->update($validated);

        // Sincronizar tipos de pessoa (many-to-many) se fornecido
        if ($personTypeIds !== null) {
            $person->personTypes()->sync($personTypeIds);
        }

        return response()->json([
            'status' => 200,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'data' => $person->load(['personTypes', 'status']),
            'message' => 'Registro atualizado com sucesso',
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Request $request, Person $person)
    {
        $person->delete();

        return response()->json([
            'status' => 200,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'message' => 'Registro excluído com sucesso',
        ]);
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(Request $request, $id)
    {
        $person = Person::withTrashed()->findOrFail($id);
        $person->restore();

        return response()->json([
            'status' => 200,
            'endpoint' => $request->method() . ' ' . $request->path(),
            'success' => true,
            'data' => $person,
            'message' => 'Registro restaurado com sucesso',
        ]);
    }
}
