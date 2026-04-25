<?php

namespace App\Models\Sanctum;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;


class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $connection = 'mysql'; // DB1 connection
}
