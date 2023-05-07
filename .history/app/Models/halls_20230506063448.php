<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class halls extends Model
{
    use HasFactory;

    protected $table = 'halls';
    protected $dates = [
        'start_party',
        'end_party',
        'created_at',
        'updated_at',

    ];
/*     protected $fillable=['name','address','rooms','chairs','price','hours','tables','type','capacity','available','person_id','verified']; */


protected $fillable=['name','address','country','city','street','rooms','chairs','price','hours','tables','type','capacity','available','start_party',
'end_party','owner_id','verified'];

    function owner()
    {
       return $this->belongsTo(Owner::class, 'owner_id');
    }


}
