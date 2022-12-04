<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @group Posts
 *
 * API endpoints for posts crud
 */
class PostController extends Controller
{
    /**
     * Display a user's posts
     *
     * @urlParam user_id integer The ID of the use.
     * @return JsonResponse
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
     * Create a new post.
     *
     * @return JsonResponse
     * @throws ValidationException
     *
     * @bodyParam title string required The post's title.
     * @bodyParam content string required The post's content.
     * @bodyParam users_id int required The related user's id.
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

        $post = Post::create($data);

        return response()->json([
            'post' => $post
        ]);
    }

    /**
     * Update a post.
     *
     * @return JsonResponse
     * @throws ValidationException
     *
     * @urlParam post_id integer The ID of the post to update
     * @bodyParam title string The post's title.
     * @bodyParam content string The post's content.
     */
    public function update(Request $request, int $post_id): JsonResponse
    {
        //Validation
        $data = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'content' => 'string|max:255',
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
            $post = Post::findOrFail($post_id);
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
     * Delete a post.
     *
     * @urlParam post_id integer The ID of the post to delete
     * @return JsonResponse
     */
    public function destroy(int $post_id)
    {
        try{
            $post = Post::findOrFail($post_id);
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
