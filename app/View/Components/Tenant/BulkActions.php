<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;

class BulkActions extends Component
{
    public array $actions;
    public string $containerId;
    public string $buttonId;
    public string $textId;

    public function __construct(
        array $actions = [],
        string $containerId = 'bulk-actions-container',
        string $buttonId = 'bulk-actions-btn',
        string $textId = 'bulk-actions-text'
    ) {
        // Se não passar ações, usa as padrões
        $this->actions = !empty($actions) ? $actions : [
            ['label' => 'Ativar', 'icon' => 'check-circle', 'color' => 'success', 'action' => 'activate'],
            ['label' => 'Desativar', 'icon' => 'cross-circle', 'color' => 'warning', 'action' => 'deactivate'],
            ['type' => 'separator'],
            ['label' => 'Deletar', 'icon' => 'trash', 'color' => 'danger', 'action' => 'delete'],
            ['label' => 'Restaurar', 'icon' => 'arrows-circle', 'color' => 'info', 'action' => 'restore'],
        ];

        $this->containerId = $containerId;
        $this->buttonId = $buttonId;
        $this->textId = $textId;
    }

    public function render()
    {
        return view('tenant.components.bulk-actions');
    }
}
