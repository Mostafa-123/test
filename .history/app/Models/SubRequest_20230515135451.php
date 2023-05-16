<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubRequest extends Model
{

    use HasFactory;

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




















}
