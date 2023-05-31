<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $table = 'special_offers';


    protected $fillable = [


        'hall_id',
        'start_date',
        'end_date',
        'price',





    ];

    protected $dates = [
        'start_party',
        'end_party',
        'created_at',
        'updated_at',

    ];


    public function halls()
    {
        return $this->belongsTo(Hall::class,'hall_id');
    }

}
