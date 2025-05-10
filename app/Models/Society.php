<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    use HasFactory;

    protected $fillable = [
        'society_name',
        'address_1',
        'address_2',
        'pincode',
        'state',
        'city'
    ];

    public function apartments()
    {
        return $this->hasMany(ApartmentDetail::class);
    }

    public function owners()
    {
        return $this->hasMany(Owner::class);
    }
}
