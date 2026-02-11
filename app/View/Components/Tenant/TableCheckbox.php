<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;

class TableCheckbox extends Component
{
    public mixed $item;
    public bool $isHeader;
    public string $targetTable;

    public function __construct(
        mixed $item = null,
        bool $isHeader = false,
        string $targetTable = ''
    ) {
        $this->item = $item;
        $this->isHeader = $isHeader;
        $this->targetTable = $targetTable;
    }

    public function render()
    {
        return view('components.tenant.table-checkbox');
    }
}
