<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;
    protected $table = 'shows';
    protected $fillable = ['showname','hall_id'];
    public function hall()
    {
        return $this->belongsTo(Hall::class,'hall_id');
    }
}
