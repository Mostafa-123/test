<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Owner;
use Illuminate\Database\Eloquent\SoftDeletes;
class PlanRequest extends Model
{
    use SoftDeletes, HasFactory;


    protected $fillable = [
        'planner_id',
        'planner_name',
        'user_id',
        'user_name',
        'plan_id',
        'plan_name',
        'price',
        'status',



    ];




    protected $appends = [
        'status_label',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $orderable = [
        'id',

        'status',
    ];

    protected $filterable = [
        'id',
        'status',
    ];









 public const STATUS_SELECT = [
    [
        'label' => 'unconfirmed',
        'value' => 'unconfirmed',
    ],
    [
        'label' => 'confirmed',
        'value' => 'confirmed',
    ],
    [
        'label' => 'cancelled',
        'value' => 'cancelled',
    ],
];




public function getStatusLabelAttribute()
{
    return collect(static::STATUS_SELECT)->firstWhere('value', $this->status)['label'] ?? '';
}


function user() {

    return $this->belongsTo(User::class, 'user_id');

}
function plannner() {

    return $this->belongsTo(Planner::class, );

}
function plan() {

    return $this->belongsTo(Plan::class,);

}

}
