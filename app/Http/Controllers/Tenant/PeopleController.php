<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeopleController extends Controller
{
    /**
     * Display a listing of people
     */
    public function index(Request $request)
    {
        $user = Auth::guard('tenant')->user();
        $tenant = request()->attributes->get('tenant');

        // Inicia a query com eager loading de WhatsApp
        $query = Person::with(['contacts' => function($query) {
            $query->whereHas('typeContact', function($q) {
                $q->where('name', 'WhatsApp');
            })->with('typeContact');
        }]);

        // ==============================================
        // FILTROS DE PESQUISA AVANÇADA
        // ==============================================

        // Filtro: Busca Rápida (campo do header)
        if ($request->filled('quick_search')) {
            $quickSearch = $request->quick_search;
            $query->where(function($q) use ($quickSearch) {
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

            $query->where(function($q) use ($searchName, $operator) {
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
            return view('tenant.people._table', [
                'people' => $people,
            ])->render();
        }

        // Se for requisição normal, retorna a view completa
        return view('tenant.people.index', [
            'people' => $people,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Reorder people via drag-and-drop
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:people,id',
            'order.*.order' => 'required|integer|min:1',
        ]);

        try {
            // Atualiza a ordem de cada pessoa
            foreach ($request->order as $item) {
                Person::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar ordem: ' . $e->getMessage()
            ], 500);
        }
    }
}
