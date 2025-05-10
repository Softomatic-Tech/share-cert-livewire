<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;
    protected $fillable = ['society_id','building_id','apartment_number','user_id','owner_name', 'email', 'phone'];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function apartment()
    {
        return $this->belongsTo(ApartmentDetail::class, 'building_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
