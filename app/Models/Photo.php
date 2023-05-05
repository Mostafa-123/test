<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $table = 'photos';
    protected $fillable = ['photoname','hall_id'];
    public function hall()
    {
        return $this->belongsTo(Hall::class,'hall_id');
    }
}
