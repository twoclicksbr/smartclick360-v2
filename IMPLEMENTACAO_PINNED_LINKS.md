# Sistema de Links Fixados (Quick Links)

**Data:** 11/02/2026
**Funcionalidade:** Permitir que usuários fixem páginas no painel Quick Links

---

## 📋 Visão Geral

Quando o usuário clicar no botão "Fixar" em qualquer página, essa página será adicionada ao painel "Quick Links" dele, criando um sistema de favoritos/atalhos personalizados.

---

## 🗄️ 1. Criar Tabela no Banco (Tenant Schema)

### Migration
```bash
php artisan make:migration create_pinned_links_table --path=database/migrations/tenant/production
```

**Arquivo:** `database/migrations/tenant/production/XXXX_create_pinned_links_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinned_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');        // Ex: "Vendas", "Produtos"
            $table->string('url');          // Ex: "/vendas", "/produtos"
            $table->string('icon')->nullable(); // Ex: "ki-dollar", "ki-tag"
            $table->string('subtitle')->nullable(); // Ex: "Gerenciar vendas"
            $table->integer('order')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['user_id', 'status']);
            $table->unique(['user_id', 'url']); // Impede duplicatas
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinned_links');
    }
};
```

**Copiar para sandbox:**
```bash
cp database/migrations/tenant/production/XXXX_create_pinned_links_table.php database/migrations/tenant/sandbox/
```

---

## 📦 2. Criar Model

**Arquivo:** `app/Models/Tenant/PinnedLink.php`

```php
<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PinnedLink extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'user_id',
        'title',
        'url',
        'icon',
        'subtitle',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // Relacionamento com User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope para pegar apenas ativos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope ordenado
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }
}
```

---

## 🎮 3. Criar Controller

**Arquivo:** `app/Http/Controllers/Tenant/PinnedLinkController.php`

```php
<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PinnedLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PinnedLinkController extends Controller
{
    /**
     * Listar links fixados do usuário
     */
    public function index()
    {
        $links = PinnedLink::where('user_id', Auth::guard('tenant')->id())
            ->active()
            ->ordered()
            ->limit(6) // Máximo de 6 links
            ->get();

        return response()->json($links);
    }

    /**
     * Fixar/adicionar um link
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:100',
            'url'      => 'required|string|max:255',
            'icon'     => 'nullable|string|max:50',
            'subtitle' => 'nullable|string|max:100',
        ]);

        $userId = Auth::guard('tenant')->id();

        // Verifica se já existe
        $existing = PinnedLink::where('user_id', $userId)
            ->where('url', $validated['url'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Este link já está fixado',
                'link' => $existing,
            ], 409);
        }

        // Conta quantos links o usuário já tem
        $count = PinnedLink::where('user_id', $userId)->count();

        if ($count >= 6) {
            return response()->json([
                'message' => 'Você atingiu o limite de 6 links fixados',
            ], 422);
        }

        // Cria o link
        $link = PinnedLink::create([
            'user_id'  => $userId,
            'title'    => $validated['title'],
            'url'      => $validated['url'],
            'icon'     => $validated['icon'] ?? 'ki-element-11',
            'subtitle' => $validated['subtitle'] ?? null,
            'order'    => $count,
        ]);

        return response()->json([
            'message' => 'Link fixado com sucesso',
            'link' => $link,
        ], 201);
    }

    /**
     * Desfixar/remover um link
     */
    public function destroy($id)
    {
        $link = PinnedLink::where('user_id', Auth::guard('tenant')->id())
            ->findOrFail($id);

        $link->delete();

        return response()->json([
            'message' => 'Link removido com sucesso',
        ]);
    }

    /**
     * Reordenar links
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'links' => 'required|array',
            'links.*.id' => 'required|exists:pinned_links,id',
            'links.*.order' => 'required|integer',
        ]);

        $userId = Auth::guard('tenant')->id();

        foreach ($validated['links'] as $linkData) {
            PinnedLink::where('id', $linkData['id'])
                ->where('user_id', $userId)
                ->update(['order' => $linkData['order']]);
        }

        return response()->json([
            'message' => 'Ordem atualizada com sucesso',
        ]);
    }
}
```

---

## 🛣️ 4. Adicionar Rotas

**Arquivo:** `routes/web.php`

Dentro do grupo `Route::middleware('auth:tenant')->group(function () {`:

```php
// Links Fixados (Quick Links)
Route::prefix('pinned-links')->name('pinned-links.')->group(function () {
    Route::get('/', [App\Http\Controllers\Tenant\PinnedLinkController::class, 'index'])
        ->name('index');
    Route::post('/', [App\Http\Controllers\Tenant\PinnedLinkController::class, 'store'])
        ->name('store');
    Route::delete('/{id}', [App\Http\Controllers\Tenant\PinnedLinkController::class, 'destroy'])
        ->name('destroy');
    Route::post('/reorder', [App\Http\Controllers\Tenant\PinnedLinkController::class, 'reorder'])
        ->name('reorder');
});
```

---

## 🎨 5. Atualizar Botão "Fixar" (JavaScript)

**Arquivo:** `resources/views/tenant/layouts/script.blade.php` (ou criar novo arquivo JS)

```javascript
// Função para fixar página atual
document.addEventListener('DOMContentLoaded', function() {
    const btnPin = document.getElementById('btn-pin-dashboard');

    if (btnPin) {
        btnPin.addEventListener('click', function(e) {
            e.preventDefault();

            // Pega informações da página atual
            const pageTitle = document.querySelector('.page-heading')?.textContent.trim().split('\n')[0] || 'Página Atual';
            const currentUrl = window.location.pathname;
            const pageIcon = 'ki-element-11'; // Pode ser dinâmico depois
            const subtitle = document.querySelector('.page-desc')?.textContent.trim() || '';

            // Verifica se já está fixado
            checkIfPinned(currentUrl).then(isPinned => {
                if (isPinned) {
                    unpinPage(currentUrl);
                } else {
                    pinPage(pageTitle, currentUrl, pageIcon, subtitle);
                }
            });
        });
    }
});

// Verificar se página já está fixada
async function checkIfPinned(url) {
    try {
        const response = await fetch('/pinned-links');
        const links = await response.json();
        return links.some(link => link.url === url);
    } catch (error) {
        console.error('Erro ao verificar links fixados:', error);
        return false;
    }
}

// Fixar página
async function pinPage(title, url, icon, subtitle) {
    try {
        const response = await fetch('/pinned-links', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ title, url, icon, subtitle })
        });

        const data = await response.json();

        if (response.ok) {
            // Atualiza UI
            updatePinButton(true);
            showNotification('Página fixada com sucesso!', 'success');
            // Recarrega Quick Links
            reloadQuickLinks();
        } else {
            showNotification(data.message || 'Erro ao fixar página', 'error');
        }
    } catch (error) {
        console.error('Erro ao fixar página:', error);
        showNotification('Erro ao fixar página', 'error');
    }
}

// Desfixar página
async function unpinPage(url) {
    try {
        const response = await fetch('/pinned-links');
        const links = await response.json();
        const link = links.find(l => l.url === url);

        if (link) {
            const deleteResponse = await fetch(`/pinned-links/${link.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (deleteResponse.ok) {
                updatePinButton(false);
                showNotification('Página desfixada', 'success');
                reloadQuickLinks();
            }
        }
    } catch (error) {
        console.error('Erro ao desfixar página:', error);
    }
}

// Atualiza visual do botão
function updatePinButton(isPinned) {
    const btn = document.getElementById('btn-pin-dashboard');
    const icon = btn.querySelector('i');
    const text = btn.querySelector('i').nextSibling;

    if (isPinned) {
        icon.classList.remove('ki-pin');
        icon.classList.add('ki-check');
        text.textContent = 'Fixado';
        btn.classList.add('btn-success');
    } else {
        icon.classList.remove('ki-check');
        icon.classList.add('ki-pin');
        text.textContent = 'Fixar';
        btn.classList.remove('btn-success');
    }
}

// Recarrega painel Quick Links
function reloadQuickLinks() {
    // Implementar reload do componente Quick Links
    // Pode ser via AJAX ou reload simples
    location.reload(); // Temporário - implementar AJAX depois
}

// Notificação (usar Toastr ou similar)
function showNotification(message, type = 'info') {
    // Implementar sistema de notificação
    alert(message); // Temporário
}
```

---

## 🔗 6. Atualizar Componente Quick Links

**Arquivo:** `resources/views/tenant/layouts/header.blade.php`

Localizar o componente Quick Links (linha ~3526) e substituir o conteúdo do grid por:

```blade
<!--begin:Nav-->
<div class="row g-0">
    @php
        // Busca links fixados do usuário
        $pinnedLinks = App\Models\Tenant\PinnedLink::where('user_id', Auth::guard('tenant')->id())
            ->active()
            ->ordered()
            ->limit(6)
            ->get();
    @endphp

    @forelse($pinnedLinks as $link)
        <!--begin:Item-->
        <div class="col-6">
            <a href="{{ url($link->url) }}"
                class="d-flex flex-column flex-center h-100 p-6 bg-hover-light border-end border-bottom position-relative">
                <i class="ki-outline {{ $link->icon }} fs-3x text-primary mb-2"></i>
                <span class="fs-5 fw-semibold text-gray-800 mb-0">{{ $link->title }}</span>
                @if($link->subtitle)
                    <span class="fs-7 text-gray-500">{{ $link->subtitle }}</span>
                @endif

                <!-- Botão remover -->
                <button class="btn btn-sm btn-icon btn-light position-absolute top-0 end-0 m-2"
                    onclick="event.preventDefault(); removePinnedLink({{ $link->id }})"
                    title="Remover">
                    <i class="ki-outline ki-cross fs-3"></i>
                </button>
            </a>
        </div>
        <!--end:Item-->
    @empty
        <div class="col-12 text-center py-10">
            <p class="text-gray-500">Nenhum link fixado ainda.</p>
            <p class="text-gray-500 fs-7">Clique em "Fixar" em qualquer página para adicionar aqui.</p>
        </div>
    @endforelse
</div>
<!--end:Nav-->

<!-- JavaScript para remover link -->
<script>
async function removePinnedLink(id) {
    if (!confirm('Deseja remover este link?')) return;

    try {
        const response = await fetch(`/pinned-links/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Erro ao remover link:', error);
    }
}
</script>
```

---

## 📊 7. Contador de Links Fixados

Atualizar o badge "25 pending tasks" para mostrar quantidade de links fixados:

```blade
<span class="badge bg-primary text-inverse-primary py-2 px-3">
    {{ $pinnedLinks->count() }} {{ $pinnedLinks->count() == 1 ? 'link fixado' : 'links fixados' }}
</span>
```

---

## 🎯 8. Melhorias Futuras

- [ ] Permitir editar título e ícone do link fixado
- [ ] Drag & drop para reordenar links
- [ ] Categorias/pastas de links
- [ ] Compartilhar links entre usuários
- [ ] Links globais do sistema (admin)
- [ ] Ícones personalizados por módulo
- [ ] Animação ao adicionar/remover
- [ ] Atalhos de teclado (Ctrl+D para fixar)

---

## ✅ Checklist de Implementação

- [ ] 1. Criar migration `create_pinned_links_table`
- [ ] 2. Copiar migration para sandbox
- [ ] 3. Rodar migrations: `php artisan migrate --path=database/migrations/tenant/production`
- [ ] 4. Criar model `PinnedLink.php`
- [ ] 5. Criar controller `PinnedLinkController.php`
- [ ] 6. Adicionar rotas em `web.php`
- [ ] 7. Adicionar JavaScript para botão "Fixar"
- [ ] 8. Atualizar componente Quick Links no `header.blade.php`
- [ ] 9. Testar fixar/desfixar páginas
- [ ] 10. Testar limite de 6 links
- [ ] 11. Testar remoção de links
- [ ] 12. Adicionar meta CSRF token no layout se não existir

---

## 🧪 Testes

```bash
# Testar fixação de página
1. Logar no sistema
2. Ir em qualquer página (ex: /dashboard/main)
3. Clicar em "Fixar"
4. Verificar se aparece no Quick Links
5. Clicar novamente para desfixar

# Testar limite
1. Fixar 6 páginas diferentes
2. Tentar fixar uma 7ª página
3. Deve mostrar erro de limite atingido

# Testar remoção
1. Abrir Quick Links
2. Clicar no X em um link
3. Confirmar remoção
4. Link deve sumir do painel
```

---

**Autor:** Sistema SmartClick360
**Versão:** 1.0
**Última atualização:** 11/02/2026
