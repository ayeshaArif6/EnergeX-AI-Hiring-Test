<?php
namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController {
    public function register(Request $req) {
    $data = $req->only(['name','email','password']);
    if (!isset($data['name'],$data['email'],$data['password'])) {
      return response()->json(['error'=>'Invalid payload'],422);
    }
    if (User::where('email',$data['email'])->exists()) {
      return response()->json(['error'=>'Email taken'],409);
    }
    $user = User::create([
      'name'=>$data['name'],
      'email'=>$data['email'],
      'password'=>password_hash($data['password'], PASSWORD_BCRYPT),
      'role'=>'user'
    ]);
    return response()->json(['id'=>$user->id],201);
  }

  public function login(Request $req) {
    $email = $req->input('email'); $pass = $req->input('password');
    $user = User::where('email',$email)->first();
    if (!$user || !password_verify($pass ?? '', $user->password)) {
      return response()->json(['error'=>'Invalid credentials'],401);
    }
    $now = time();
    $token = JWT::encode([
      'iss'=>'energeX','iat'=>$now,'exp'=>$now + (int)env('JWT_TTL',3600),'sub'=>$user->id
    ], env('JWT_SECRET'), 'HS256');
    return response()->json(['token'=>$token,'user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email]]);
  }
    
}
