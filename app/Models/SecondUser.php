<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class SecondUser extends Authenticatable
{
    use HasApiTokens;

    protected $connection = 'mysql_second';
    protected $table = 'users';
}