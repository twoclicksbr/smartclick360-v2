<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;

class QuickSearch extends Component
{
    public string $placeholder;
    public string $name;
    public ?string $value;
    public string $action;

    public function __construct(
        string $placeholder = 'Buscar',
        string $name = 'quick_search',
        ?string $value = null,
        ?string $action = null
    ) {
        $this->placeholder = $placeholder;
        $this->name = $name;
        $this->value = $value ?? request($name);
        $this->action = $action ?? url()->current();
    }

    public function render()
    {
        return view('components.tenant.quick-search');
    }
}
