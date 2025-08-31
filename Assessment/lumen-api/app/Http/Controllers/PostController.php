
<?php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Post;
use Predis\Client as RedisClient;


class PostController extends BaseController
{
    private RedisClient $redis;
    private int $ttl;

    public function __construct()
    {
        $this->redis = new RedisClient([
            'host' => env('REDIS_HOST', 'redis'),
            'port' => (int) env('REDIS_PORT', 6379),
        ]);
        $this->ttl = (int) env('CACHE_TTL_SECONDS', 120); 
    }

    public function index()
    {
        $key = 'posts:all';
        if ($this->redis->exists($key)) {
            return response()->json(json_decode($this->redis->get($key), true));
        }

        $posts = Post::orderBy('created_at', 'desc')->get();
        $this->redis->setex($key, $this->ttl, $posts->toJson());

        return response()->json($posts);
    }

    public function show($id)
    {
        $key = "posts:$id";
        if ($this->redis->exists($key)) {
            return response()->json(json_decode($this->redis->get($key), true));
        }

        $post = Post::find($id);
        if (!$post) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $this->redis->setex($key, $this->ttl, $post->toJson());
        return response()->json($post);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('user');
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $title = trim((string) $request->input('title', ''));
        $content = trim((string) $request->input('content', ''));

        if ($title === '' || $content === '') {
            return response()->json(['error' => 'title and content are required'], 422);
        }

        $post = Post::create([
            'title'   => $title,
            'content' => $content,
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        $this->redis->del(['posts:all']);
        $this->redis->setex("posts:{$post->id}", $this->ttl, $post->toJson());

        return response()->json($post, 201);
    }
}
