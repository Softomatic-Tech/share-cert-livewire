<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApartmentDetail extends Model
{
    use HasFactory;

    protected $fillable = ['society_id', 'building_name', 'apartment_number'];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function owners()
    {
        return $this->hasMany(Owner::class,'apartment_detail_id');
    }
}
