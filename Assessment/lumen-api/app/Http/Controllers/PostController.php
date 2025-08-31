<?php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Post;

class PostController extends BaseController
{
    public function index()
    {
        try {
            $posts = Post::orderBy('id', 'desc')->get();
            return response()->json($posts, 200);
        } catch (\Throwable $e) {
            Log::error('Posts index error', ['ex' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function show($id)
    {
        try {
            $post = Post::find($id);
            if (!$post) return response()->json(['error' => 'Not found'], 404);
            return response()->json($post, 200);
        } catch (\Throwable $e) {
            Log::error('Posts show error', ['ex' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->attributes->get('user');
            if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

            $title   = trim((string)$request->input('title', ''));
            $content = trim((string)$request->input('content', ''));

            if ($title === '' || $content === '') {
                return response()->json(['error' => 'title and content are required'], 422);
            }

            $post = Post::create([
                'title'   => $title,
                'content' => $content,
                'user_id' => $user->id,
            ]);

            return response()->json($post, 201);
        } catch (\Throwable $e) {
            Log::error('Posts store error', ['ex' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $user = $request->attributes->get('user');
            if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

            $post = Post::find($id);
            if (!$post) return response()->json(['error' => 'Not found'], 404);
            if ((int)$post->user_id !== (int)$user->id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $title   = trim((string)$request->input('title', $post->title));
            $content = trim((string)$request->input('content', $post->content));

            if ($title === '' || $content === '') {
                return response()->json(['error' => 'title and content are required'], 422);
            }

            $post->title = $title;
            $post->content = $content;
            $post->save();

            return response()->json($post, 200);
        } catch (\Throwable $e) {
            Log::error('Posts update error', ['ex' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $user = $request->attributes->get('user');
            if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

            $post = Post::find($id);
            if (!$post) return response()->json(['error' => 'Not found'], 404);
            if ((int)$post->user_id !== (int)$user->id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $post->delete();
            return response()->json(['ok' => true], 200);
        } catch (\Throwable $e) {
            Log::error('Posts destroy error', ['ex' => $e->getMessage()]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
