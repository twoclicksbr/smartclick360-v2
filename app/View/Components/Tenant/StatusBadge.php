<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;

class StatusBadge extends Component
{
    public bool $status;
    public string $activeText;
    public string $inactiveText;

    public function __construct(
        bool $status = true,
        string $activeText = 'Ativo',
        string $inactiveText = 'Inativo'
    ) {
        $this->status = $status;
        $this->activeText = $activeText;
        $this->inactiveText = $inactiveText;
    }

    public function render()
    {
        return view('tenant.components.status-badge');
    }
}
