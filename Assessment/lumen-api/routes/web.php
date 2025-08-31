<?php

$router->get('/', fn() => response()->json(['ok'=>true,'service'=>'lumen-api'] ));
$router->get('/ping', fn() => response('pong',200));

$router->post('/api/register','AuthController@register');
$router->post('/api/login','AuthController@login');

$router->get('/api/posts',        'PostController@index');
$router->get('/api/posts/{id}',   'PostController@show');

$router->post('/api/posts', ['middleware' => 'jwt', 'uses' => 'PostController@store']);
