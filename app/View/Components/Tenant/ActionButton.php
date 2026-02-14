<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;

class ActionButton extends Component
{
    public string $label;
    public string $icon;
    public ?string $href;
    public ?string $modal;
    public string $color;
    public string $size;
    public string $id;
    public string $iconLibrary;

    public function __construct(
        string $label,
        string $icon = 'plus',
        ?string $href = null,
        ?string $modal = null,
        string $color = 'light-primary',
        string $size = 'sm',
        string $id = '',
        string $iconLibrary = 'kt'
    ) {
        $this->label = $label;
        $this->icon = $icon;
        $this->href = $href ?? '#';
        $this->modal = $modal;
        $this->color = $color;
        $this->size = $size;
        $this->id = $id;
        $this->iconLibrary = $iconLibrary;
    }

    public function render()
    {
        return view('tenant.components.action-button');
    }
}
