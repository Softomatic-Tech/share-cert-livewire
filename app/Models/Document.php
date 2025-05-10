<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'file_path'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
