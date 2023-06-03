<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;



    protected $fillable=[
        'hall_id',
        'user_id',
    ];

/*     protected $appends = [

        'human_readabale_created_at'


    ]; */





    public function halls()
    {
        return $this->belongsTo(Hall::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);


    }



    public function getHumanReadableCreatedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

}
