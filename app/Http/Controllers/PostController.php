<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index(int $user_id)
    {
        try {
            //Retrieve user
            $user = User::findOrFail($user_id);

            return response()->json([
                'posts' => $user->posts
            ]);
        } catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validation
        $data = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required|max:255',
            'users_id' => ['required']
        ]);

        //Checking for errors
        if ($data->fails()){
            return response()->json([
                'error' => $data->errors()
            ]);
        }

        //Object to array conversion
        $data = $data->validate();

        return response()->json([
            'post' => Post::create($data)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, $id)
    {
        //Validation
        $data = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required|max:255',
        ]);

        //Checking for errors
        if ($data->fails()){
            return response()->json([
                'error' => $data->errors()
            ]);
        }

        //Object to array conversion
        $data = $data->validate();

        try{
            $post = Post::findOrFail($id);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }

        $post->update($data);

        return response()->json([
            'post' => $post,
            'message' => 'Post updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy($id)
    {
        try{
            $post = Post::findOrFail($id);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ]);
        }

        $user = $post->user;
        $post->delete();

        //Return user's posts
        return response()->json([
            'posts' => $user->posts,
            'message' => 'Post deleted'
        ]);
    }
}
