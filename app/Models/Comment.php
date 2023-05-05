<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Comment extends Model
{
    use HasFactory;



    protected $fillable=[
        'hall_id',
        'user_id',
        'content',
    ];

    protected $appends = [

        'Published'


    ];







    public function halls()
    {
        return $this->belongsTo(Hall::class);
    }


    public function users()
    {
        return $this->belongsTo(User::class,'user_id')->select('email', 'name');
    }
    public function userss()
    {
        return $this->belongsTo(users::class);
    }
    public function admins()
    {
        return $this->belongsTo(Admin::class,'user_id')->select('email', 'name');
    }



    public function getPublishedAttribute(){

        return Carbon::createFromTimeStamp(strtotime($this->attributes['created_at']) )->diffForHumans();
    }
}
