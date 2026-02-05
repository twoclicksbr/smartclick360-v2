<?php

namespace App\Models\Passport;

use App\Traits\UsesTenantConnection;
use Laravel\Passport\Client as PassportClient;

class Client extends PassportClient
{
    use UsesTenantConnection;
}
