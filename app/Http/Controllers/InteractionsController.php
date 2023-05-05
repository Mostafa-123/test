<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use App\Models\Hall;
use App\Models\User;
use App\Models\Admin;
use App\Models\Comment;
use App\Models\Like;
use App\Http\Traits\GeneralTraits;

class InteractionsController extends Controller
{
    //
    use GeneralTraits;


    public function addComment(Request $request,  $hall_id)
    {
        $thehall = Hall::where('id',$hall_id)->first();

        if($thehall)/* Check if Hall Id is valid */{


            $rules = [
                'content' => 'required|string',
            ];
            $validator = Validator::make($request->all(),$rules);
            if ($validator->fails())/* Check if Comment's content is valid */ {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            else{
            $comment = Comment :: create ([
                'hall_id'=>$thehall->id,
                'user_id'=> auth()->user()->id,
                'content'=>$request->content,
            ]);
            $comment->load('users');
            $comment->load('admins');
            $comment->load('users');
            $comment->load('halls');
            return response()->json(['message' => 'Comment added.']);}
        }
        else{
            response()->json(['message' => 'HALL ID IS WRONG.']);
        }





    }


    public function getComment(Request $request,  $hall_id)
    {
        $thehall = Hall::where('id',$hall_id)->first();
        $limit = 2;
        if($thehall){ response()->json(['message' => 'HALL ID.']);
            $thecomment = Comment::where('hall_id',$hall_id)->first();
            if($thecomment){
                $comments = Comment::with(['users' => function($q) {
                    $q->select('id','email', 'name');}])
                    ->where('hall_id',$hall_id)->orderBy('id')->get();

                return   response()->json(['message' => 'Comments Successfully Fetched .',
                                            'data'=> $comments
                            ],200);


        }
        else{
            return response()->json(['message' => 'There is no Comments.'],400);
        }




        }
        else{
            return  response()->json(['message' => 'HALL ID IS WRONG.']);
        }



    }


    public function updateComment(Request $request,  $comment_id)
    {

        $comments = Comment::with(['users'])
            ->where('id',$comment_id)->first();

        if ($comments){
            if ($comments->user_id==$request->user()->id){

                $rules = [
                    'content' => 'required|string',
                ];
                $validator = Validator::make($request->all(),$rules);
                if ($validator->fails())/* Check if Comment's content is valid */ {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);

                }
                else{
                    $comments->Update([
                        'content'=>$request->content
                    ]);
                    return   response()->json(['message' => 'Comments Successfully Updated .',
                    'data'=> $comments
                    ],200);
                }
            }   else{
                return  response()->json(['message' => 'Access denied There is no comments here.']);
            }


        }    else{
            return  response()->json(['message' => 'Comment not found.']);
        }

    }

    public function deleteComment(Request $request,  $comment_id)
    {
            $comments = Comment::with(['users'])
            ->where('id',$comment_id)->first();

        if ($comments){
            if ($comments->user_id==$request->user()->id){



                $comments->delete();
                return   response()->json(['message' => 'Comments Successfully Deleted .',

                ],200);




            }   else{
                    return  response()->json(['message' => 'Access denied  here.']);
                }
            }    else{
                return  response()->json(['message' => 'Comment not found.']);
            }

    }


    public function addLike(Request $request,  $hall_id)
    {

        $thehall = Hall::where('id',$hall_id)->first();

        if($thehall)/* Check if Hall Id is valid */{
                $like = Like :: create ([
                    'hall_id'=>$thehall->id,
                    'user_id'=>  auth()->user()->id,
                ]);
                $like->load('users');
                $like->load('halls');
        return response()->json(['message' => 'Post liked.']);
        }
        else{
            response()->json(['message' => 'HALL ID IS WRONG.']);
        }


    }


    public function removeLike(Request $request,  $like_id)
    {
        $likes = Like::with(['users'])
        ->where('id',$like_id)->first();

        if($likes)/* Check if Hall Id is valid */{
            if ($likes->user_id==$request->user()->id){
                $likes->delete();
                return   response()->json(['message' => 'likes Successfully Deleted .',

                ],200);


            }   else{
                return  response()->json(['message' => 'Access denied  here.']);
            }
        }    else{
            return  response()->json(['message' => 'like not found.']);
        }

    }









}
