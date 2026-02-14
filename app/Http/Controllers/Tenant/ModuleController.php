<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    /**
     * Verifica se existe um controller específico para o módulo
     * Ex: people → PeopleController, products → ProductsController
     */
    private function getSpecificController(string $module)
    {
        $controllerClass = 'App\\Http\\Controllers\\Tenant\\' . Str::studly($module) . 'Controller';

        if (class_exists($controllerClass)) {
            return app($controllerClass);
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slug, string $module)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'index')) {
            return $specificController->index($request, $slug, $module);
        }

        // Lógica genérica (ainda não implementada)
        abort(404, 'Módulo não encontrado');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'create')) {
            return $specificController->create($slug, $module);
        }

        // Lógica genérica (ainda não implementada)
        abort(404, 'Módulo não encontrado');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug, string $module)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'store')) {
            return $specificController->store($request, $slug, $module);
        }

        // Lógica genérica (ainda não implementada)
        abort(404, 'Módulo não encontrado');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug, string $module, string $code)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'show')) {
            return $specificController->show($slug, $module, $code);
        }

        // Lógica genérica (ainda não implementada)
        abort(404, 'Módulo não encontrado');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $code)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'edit')) {
            return $specificController->edit($slug, $module, $code);
        }

        // Lógica genérica (ainda não implementada)
        abort(404, 'Módulo não encontrado');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'update')) {
            return $specificController->update($request, $slug, $module, $code);
        }

        // Lógica genérica (ainda não implementada)
        abort(404, 'Módulo não encontrado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug, string $module, string $code)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'destroy')) {
            return $specificController->destroy($slug, $module, $code);
        }

        // Lógica genérica (ainda não implementada)
        abort(404, 'Módulo não encontrado');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $code)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'restore')) {
            return $specificController->restore($slug, $module, $code);
        }

        // Lógica genérica (ainda não implementada)
        abort(404, 'Módulo não encontrado');
    }

    /**
     * Reorder resources (drag and drop)
     */
    public function reorder(Request $request, string $slug, string $module)
    {
        // Verifica se existe controller específico
        $specificController = $this->getSpecificController($module);

        if ($specificController && method_exists($specificController, 'reorder')) {
            return $specificController->reorder($request, $slug, $module);
        }

        // Lógica genérica: mapeia módulo para model
        $moduleModelMap = [
            'people' => \App\Models\Tenant\Person::class,
            // Adicione outros módulos conforme necessário
            // 'products' => \App\Models\Tenant\Product::class,
            // 'sales' => \App\Models\Tenant\Sale::class,
        ];

        // Verifica se o módulo existe no mapeamento
        if (!isset($moduleModelMap[$module])) {
            return response()->json(['success' => false, 'message' => 'Módulo não suporta reordenação'], 404);
        }

        $modelClass = $moduleModelMap[$module];
        $order = $request->input('order', []);

        foreach ($order as $item) {
            $modelClass::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
