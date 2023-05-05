<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $fillable = ['servicename','hall_id'];
    public function hall()
    {
        return $this->belongsTo(Hall::class,'hall_id');
    }
}
