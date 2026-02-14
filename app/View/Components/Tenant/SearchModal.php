<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;

class SearchModal extends Component
{
    public function __construct()
    {
        //
    }

    public function render()
    {
        return view('tenant.components.search-modal');
    }
}
