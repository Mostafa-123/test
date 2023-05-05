<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Booking;
use App\Models\Admin;
use App\Models\User;


class Hall extends Model
{
    use HasFactory;

    protected $table = 'halls';
    protected $dates = [
        'start_party',
        'end_party',
        'created_at',
        'updated_at',

    ];

    protected $appends = [

        'comments_count ',
        'likes_count ',


    ];

    protected $fillable=['name','address','country','city','street','rooms','chairs','price','hours','tables','type','capacity','available','start_party',
    'end_party','owner_id','verified'];



    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function shows()
    {
        return $this->hasMany(Show::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    function Owner()
    {
        $this->belongsTo(Owner::class);
    }


    public function likes()
{
    return $this->hasMany(like::class);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}


public function getCommentsCountAttribute()
{
    return $this->comments()->count();
}
public function getLikesCountAttribute()
{
    return $this->likes()->count();
}


public function offers()
{
    return $this->hasMany(Offer::class);
}








    protected function serializeDate(DateTimeInterface $date)
{
    return $date->format('Y-m-d H:i:s');
}

public function getStartPartyAttribute($value)
{
    return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
}

public function setStartPartyAttribute($value)
{
    $this->attributes['start_party'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
}

public function getEndPartyAttribute($value)
{
    return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
}

public function setEndPartyAttribute($value)
{
    $this->attributes['end_party'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
}

public function confirmedBookings()
{
    return $this->hasMany(Booking::class)->where('status', 'confirmed');
}

public function rejectedBookings()
{
    return $this->hasMany(Booking::class)->where('status', 'cancelled');
}

public function processingBookings()
{
    return $this->hasMany(Booking::class)->where('status', 'unconfirmed');
}

}
