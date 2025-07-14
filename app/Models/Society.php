<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    use HasFactory;

    protected $fillable = [
        'society_name',
        'total_flats',
        'address_1',
        'address_2',
        'pincode',
        'state',
        'city'
    ];

    public function details()
    {
        return $this->hasMany(SocietyDetail::class);
    }
}
