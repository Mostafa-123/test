<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\User;
use App\Models\Owner;
use App\Models\Hall;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;



class Booking extends Model
{
    use SoftDeletes, HasFactory;


    protected $fillable = [
        'user_name',
        'hall_name',
        'hall_id',
        'check_in_date',
        'check_out_date',
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
        'halls.name',
        'name',
        'status',
    ];

    protected $filterable = [
        'id',
        'halls.name',
        'name',
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

protected function serializeDate(DateTimeInterface $date)
{
    return $date->format('Y-m-d H:i:s');
}


public function getStatusLabelAttribute()
{
    return collect(static::STATUS_SELECT)->firstWhere('value', $this->status)['label'] ?? '';
}



    function admin() {

        return $this->belongsTo(admin::class, 'user_id');

    }
    function user() {

        return $this->belongsTo(User::class, 'user_id');

    }
    function owner() {

        return $this->belongsTo(Owner::class, );

    }
    function hall() {

        return $this->belongsTo(hall::class,'hall_id');

    }

}
