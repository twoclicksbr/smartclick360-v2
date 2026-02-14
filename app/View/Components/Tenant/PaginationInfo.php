<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginationInfo extends Component
{
    public LengthAwarePaginator $paginator;

    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function render()
    {
        return view('tenant.components.pagination-info');
    }
}
