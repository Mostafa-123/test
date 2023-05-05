<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

use App\Http\Traits\GeneralTraits;
class CategoryController extends Controller
{
    use GeneralTraits;
    public function index(){


        Category::get();

        $categories = Category::select('id','name_'.app()->getLocale())-> get();
        return response()->json($categories);
    }

    public function cat_id (Request $request){


        $category = Category::select()->find($request->id);
        if (!$category)
            return $this->returnError('001', 'هذا القسم غير موجد');

        return $this->returnData('categroy', $category);

    }


    public function change_status(Request $request){

/*
        $category = Category::select()->find($request->id);
        if (!$category)
            return $this->returnError('001', 'هذا القسم غير موجد'); */
        Category::where('id',$request -> id) -> update(['active' =>$request ->active]);

        return $this -> returnSuccessMessage('DONE');



    }

}
