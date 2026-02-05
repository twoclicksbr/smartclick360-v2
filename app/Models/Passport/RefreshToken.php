<?php

namespace App\Models\Passport;

use App\Traits\UsesTenantConnection;
use Laravel\Passport\RefreshToken as PassportRefreshToken;

class RefreshToken extends PassportRefreshToken
{
    use UsesTenantConnection;
}
