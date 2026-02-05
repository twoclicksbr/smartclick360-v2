<?php

namespace App\Models\Passport;

use App\Traits\UsesTenantConnection;
use Laravel\Passport\AuthCode as PassportAuthCode;

class AuthCode extends PassportAuthCode
{
    use UsesTenantConnection;
}
