<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;
    protected $fillable = ['apartment_detail_id','user_id','owner_name', 'email', 'phone'];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }

    public function apartment()
    {
        return $this->belongsTo(ApartmentDetail::class, 'apartment_detail_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
