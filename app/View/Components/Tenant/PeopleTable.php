<?php

namespace App\View\Components\Tenant;

use Illuminate\View\Component;

class PeopleTable extends Component
{
    public $people;

    public function __construct($people)
    {
        $this->people = $people;
    }

    public function render()
    {
        return view('tenant.components.people-table');
    }
}
