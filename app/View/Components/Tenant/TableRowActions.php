<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;

class TableRowActions extends Component
{
    public array $actions;
    public mixed $item;

    public function __construct(
        array $actions = [],
        mixed $item = null
    ) {
        // Se não passar ações, usa as padrões
        $this->actions = !empty($actions) ? $actions : [
            ['label' => 'Editar', 'icon' => 'pencil', 'color' => 'light-primary', 'action' => 'edit'],
            ['label' => 'Excluir', 'icon' => 'trash', 'color' => 'light-danger', 'action' => 'delete'],
        ];

        $this->item = $item;
    }

    public function render()
    {
        return view('components.tenant.table-row-actions');
    }
}
