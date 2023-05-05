<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $table = 'videos';
    protected $fillable = ['videoname','hall_id'];
    public function hall()
    {
        return $this->belongsTo(Hall::class,'hall_id');
    }
}
