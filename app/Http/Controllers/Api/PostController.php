<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    //
    public function index()
    {
        //only if the user has the 'view permission' permission
        if (!Auth::user()->hasPermissionTo('view post', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $posts = Post::with('author')->get();
        return response()->json([
            'message' => 'Lists of posts',
            'posts' => $posts
        ]);
    }

    public function show($id)
    {
        //only if the user has the 'view permission' permission
        if (!Auth::user()->hasPermissionTo('edit post', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $post = Post::where('id', '=', $id)->with('author')->first();
        return response()->json([
            'message' => 'Post Details',
            'post' => $post
        ]);
    }

    public function store(Request $request)
    {
        //only if the user has the 'view permission' permission
        if (!Auth::user()->hasPermissionTo('create post', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $validated = $request->validate([
            'title' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'content' => 'required',
        ]);


        $image = $request->file('image');
        $imageName = null;

        if ($image) {
            $image->storeAs('public/image/posts', $image->hashName());
            $imageName = $image->hashName();
        }

        $post = Post::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'image' => $imageName,
            'content' => $validated['content'],
            'published_at' => now(),
            'author' => Auth::id()
        ]);


        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ]);
    }

    public function update(Request $request, $id)
    {
        //only if the user has the 'view permission' permission
        if (!Auth::user()->hasPermissionTo('edit post', 'api')) {
            return response()->json(['message' => 'You dont have the access of permission'], 403);
        }

        $validated = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $post = Post::where('id', '=', $id)->first();

        //check if image is not empty
        if ($request->hasFile('image')) {

            //delete old image
            Storage::delete('image/posts/' . basename($post->image));

            //upload image
            $image = $request->file('image');
            $image->storeAs('image/posts', $image->hashName());

            //update post with new image
            $post->update([
                'image' => $image->hashName(),
                'title' => $validated['title'],
                'content' => $validated['content'],
            ]);
        } else {

            //update post without image
            $post->update([
                'title'   => $validated['title'],
                'content' => $validated['content'],
            ]);
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }

    public function destroy($id)
    {
        //only if the user has the 'delete user' permission
        if (!Auth::user()->hasPermissionTo('delete post', 'api')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post = Post::findOrFail($id);
        $post->delete();

        // Logic to delete a user
        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
