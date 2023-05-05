<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Planner;
use App\Models\PlanRequest;


class Plan extends Model
{
    use HasFactory;



    protected $table = 'plans';



    protected $fillable=['name','description','price'];
    function planner()
    {
        $this->belongsTo(Planner::class);
    }

    public function planPhotos()
    {
        return $this->hasMany(PlanPhoto::class);
    }

    function planRequest()
    {
        $this->hasMany(PlanRequest::class);
    }





}






