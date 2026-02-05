<?php

namespace App\Models\Passport;

use App\Traits\UsesTenantConnection;
use Laravel\Passport\Token as PassportToken;

class Token extends PassportToken
{
    use UsesTenantConnection;
}
