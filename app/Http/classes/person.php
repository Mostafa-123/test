<?php

namespace App\Http\classes;

use App\Http\Resources\hallResource;
use App\Http\Resources\personResource;
use App\Http\responseTrait;
use App\Models\halls;
use App\Models\User;
use App\Models\users;
use Illuminate\Http\Request;

trait person
{
    use responseTrait;
    public function updateAnyInfoInPerson(Request $req,$id){
        $result=User::find($id);
        if(!$result){
            return $this->response(null,'The person Not Found',404);
        }

        $result->update([
            'name'=>$req->name?$req->name:$result->name,
            'email'=>$req->email?$req->email:$result->email,
            'password'=>$req->password?$req->password:$result->password,
            'country'=>$req->country?$req->country:$result->country,
            'religion'=>$req->religion?$req->religion:$result->religion,
            'gender'=>$req->gender?$req->gender:$result->gender,
            'national_id'=>$req->national_id?$req->national_id:$result->national_id,
        ]);

        if($result){
            return $this->response(new personResource($result),'The information update',201);
        }
    }
    public function addComment(Request $req,$id){
        $result=User::find($id);
        if(!$result){
            return $this->response(null,'The person Not Found',404);
        }

        $result->update([
            'comments'=>$req->comments,
        ]);

        if($result){
            return $this->response(new personResource($result),'thank you we will see your comment and try to solve them in nearest time',201);
        }
    }
}
