<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPhoto extends Model
{
    use HasFactory;
    protected $table = 'plan_photos';
    protected $fillable = ['photoname','plan_id'];
    public function plan()
    {
        return $this->belongsTo(Plan::class,'plan_id');
    }
}
