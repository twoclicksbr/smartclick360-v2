# Modal de Pesquisa Avançada - Guia de Uso

## 📍 Localização
`resources/views/tenant/search/modal.blade.php`

## 🎯 Componente Genérico e Reutilizável

Este modal foi criado para ser usado em **todos os módulos** do sistema. Ele já inclui:

- ✅ JavaScript (Select2, daterangepicker, submit handler)
- ✅ Campos padrão (ID, datas, status, deletados, paginação)
- ✅ Campo principal parametrizável

---

## 📖 Exemplos de Uso

### 1. Pessoas (padrão - não precisa passar parâmetro)
```blade
@include('tenant.search.modal')
```
**Resultado:** Campo de busca com placeholder "Nome"

---

### 2. Produtos
```blade
@include('tenant.search.modal', [
    'searchFieldPlaceholder' => 'Nome do Produto'
])
```

---

### 3. Fornecedores
```blade
@include('tenant.search.modal', [
    'searchFieldPlaceholder' => 'Razão Social'
])
```

---

### 4. Vendas
```blade
@include('tenant.search.modal', [
    'searchFieldPlaceholder' => 'Cliente ou Número da Venda'
])
```

---

### 5. Usuários
```blade
@include('tenant.search.modal', [
    'searchFieldPlaceholder' => 'Nome ou E-mail'
])
```

---

## 🔧 Campos do Modal

| Campo | Tipo | Nome do Input | Descrição |
|-------|------|---------------|-----------|
| ID | text | `search_id` | Busca por ID |
| Operador | select | `search_operator` | Contém / Início exato / Exato |
| **Campo Principal** | text | `search_name` | **Parametrizável** via `$searchFieldPlaceholder` |
| Campo de Data | select | `search_date_field` | created_at / updated_at / deleted_at |
| Período | daterangepicker | `search_date_range` | Ranges predefinidos em português |
| Por Página | select | `search_per_page` | 10 / 25 / 50 / 100 / 250 |
| Status | select | `search_status` | Todos / Ativo / Inativo |
| Exibir deletados | checkbox | `search_deleted` | Checkbox switch |

---

## 💡 Dicas

1. **Campo padrão é "Nome"** - Use sem parâmetros quando o módulo tiver um campo "Nome"
2. **JavaScript incluído** - Não precisa adicionar código JS na página (já vem no modal via `@push('scripts')`)
3. **Select2 e daterangepicker** - Inicializados automaticamente
4. **Personalizável** - Se algum módulo precisar de campos extras, crie uma variante específica

---

## 🚀 Próximos Passos

Quando for implementar a funcionalidade real de busca:

1. Capturar os dados no controller
2. Aplicar filtros na query
3. Retornar resultados filtrados
4. Implementar paginação

**Exemplo de captura no Controller:**
```php
public function index(Request $request)
{
    $query = Model::query();

    if ($request->filled('search_id')) {
        $query->where('id', $request->search_id);
    }

    if ($request->filled('search_name')) {
        $operator = $request->search_operator ?? 'contains';

        switch($operator) {
            case 'contains':
                $query->where('name', 'like', '%' . $request->search_name . '%');
                break;
            case 'starts_with':
                $query->where('name', 'like', $request->search_name . '%');
                break;
            case 'exact':
                $query->where('name', $request->search_name);
                break;
        }
    }

    // ... demais filtros

    $results = $query->paginate($request->search_per_page ?? 25);

    return view('module.index', compact('results'));
}
```

---

**Criado em:** 2026-02-11
**Versão:** 1.0
