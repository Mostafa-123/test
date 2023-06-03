<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\SubService;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Booking;

class SubRequest extends Model
{

    use SoftDeletes,HasFactory;

    protected $fillable = [
        'supplier_id',
        'supplier_name',
        'user_id',
        'user_name',
        'role',
        'sub_id',
        'sub_name',
        'price',
        'check_in_date',
        'check_out_date',
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


function user() {

    return $this->belongsTo(User::class, 'user_id');

}
function plannner() {

    return $this->belongsTo(Planner::class,'planner_id' );

}
function Subservice() {

    return $this->belongsTo(SubService::class, 'plan_id');

}



















}
