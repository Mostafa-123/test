<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Supplier;

class SubService extends Model
{
    use HasFactory;
    protected $table = 'sub_services';



    protected $fillable=['name','supplier_id','description','price','country','city','address','type'];

    public function servicesPhoto()
    {
        return $this->hasMany(SupPhoto::class,'service_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
