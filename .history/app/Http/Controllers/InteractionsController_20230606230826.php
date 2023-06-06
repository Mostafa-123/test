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
use App\Models\Favourite;
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


    public function addLike1(Request $request,  $hall_id)
    {
        $userid = Auth::guard('user-api')->id();

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
            return $this->removeLike($hall_id);
        }


    }


    public function removeLike(Request $request,  $like_id)
    {
        $likes = Like::with(['users'])
        ->where('id',$like_id)->first();

        if($likes)/* Check if Hall Id is valid */{
            if ($likes->user_id==Auth::guard('user-api')->user()->id){
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





    public function addLike2(Request $request, $hall_id)
    {
        $userid = Auth::guard('user-api')->id();

        $thehall = Hall::where('id', $hall_id)->first();

        if ($thehall)/* Check if Hall Id is valid */
        {
            $like = Like::where('hall_id', $thehall->id)
                ->where('user_id', auth()->user()->id)
                ->first();

            if ($like) {
                $like->delete();
                return response()->json(['message' => 'Like removed.']);
            } else {
                return response()->json(['message' => 'Like not found.']);
            }
        } else {
            return response()->json(['message' => 'Invalid hall ID.']);
        }
    }


    public function addLike(Request $request, $hall_id)
    {
        $userid = Auth::guard('user-api')->id();

        $thehall = Hall::where('id', $hall_id)->first();

        if ($thehall)/* Check if Hall Id is valid */
        {
            $existingLike = Like::where('hall_id', $thehall->id)
                ->where('user_id', auth()->user()->id)
                ->first();

            if ($existingLike) {
                return response()->json(['message' => 'You have already liked this hall.']);
            } else {
                $like = Like::create([
                    'hall_id' => $thehall->id,
                    'user_id' => auth()->user()->id,
                ]);
                $like->load('users');
                $like->load('halls');
                return response()->json(['message' => 'Post liked.']);
            }
        } else {
            return response()->json(['message' => 'Invalid hall ID.']);
        }
    }







    public function addFavourite($hall_id)
{
    $thehall = Hall::where('id', $hall_id)->where('verified', 'confirmed')->first();
    $user_id = Auth::guard('user-api')->user()->id;

    if ($thehall) {
        $fav = Favourite::where('hall_id', $hall_id)->where('user_id', $user_id)->first();

        if (!$fav) {
            $like = Favourite::create([
                'hall_id' => $thehall->id,
                'user_id' => $user_id,
                'user_name' => Auth::guard('user-api')->user()->name,
            ]);

            $like->load('users');
            $like->load('halls');

            return response()->json(['message' => 'Added To Favorites.']);
        } else {
            return $this->removeFavourite($hall_id);
        }
    } else {
        return response()->json(['message' => 'HALL ID IS WRONG.'], 404);
    }
}



    public function removeFavourite($hall_id)
    {
        $fav = Favourite::with(['users'])
        ->where('hall_id',$hall_id)->first();

        if($fav)/* Check if Hall Id is valid */{
            if ($fav->user_id==Auth::guard('user-api')->user()->id){
                $fav->delete();
                return   response()->json(['message' => 'UnFavourited Successfully  .',

                ],200);


            }   else{
                return  response()->json(['message' => 'Access denied  here.'],403);
            }
        }    else{
            return  response()->json(['message' => ' not in favourites found.'],404);
        }

    }



    public function getFavourite(Request $requestd)
    {

        $user=Auth::guard('user-api')->user();
        $favorites = $user->favourites;
        if($favorites){
            return response()->json([

                'message'=>'Returned Successfully ',
                'favourites'=>$favorites

            ],200);

        }
        else{
            return  response()->json(['message' => 'NOT Found'],404);
        }



    }




    public function getUserFavsHalls()
    {
        $user = Auth::guard('user-api')->user();
        $favorites = $user->favourites->pluck('hall_id');

        $halls = Hall::whereIn('id', $favorites)->get();

        if($halls){

            return response()->json([
                'message' => 'Halls with IDs retrieved successfully.',
                'halls' => $halls
            ], 200);

        }
        else{
            return  response()->json(['message' => 'NOT Found'],404);
        }

    }

public function isFavourite ($hallId){

    // $hallId = $request->input('hall_id');


    $user = Auth::guard('user-api')->user();
    $userid = Auth::guard('user-api')->id();

    if (!$user) {
        return response()->json(['message' => 'User not authenticated.'], 401);
    }

    // $isFavourite = $user->favourites()->where('hall_id', $hallId)->where('user_id', $userid)->exists();
    $isFavourite = Favourite::where('hall_id', $hallId)->where('user_id', $userid)->exists();
        if($isFavourite){
            return response()->json([
                'message' => 'Check completed successfully.',
                'is_favourite' => true
            ], 200);
        }else{

            return response()->json([
                'message' => 'Check Failed .',
                'is_favourite' => false
            ], 405);
        }

}



}
