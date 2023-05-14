<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupPhoto extends Model
{
    use HasFactory;
    protected $table = 'sup_photos';
    protected $fillable = ['photoname','service_id'];
    public function supService()
    {
        return $this->belongsTo(SubService::class,'service_id');
    }
}
