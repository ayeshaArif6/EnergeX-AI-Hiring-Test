<?php

$router->get('/', fn() => response()->json(['ok'=>true,'service'=>'lumen-api'] ));
$router->get('/api/ping', fn() => response()->json(['message' => 'pong']));

$router->get('/ping', fn() => response('pong',200));

$router->post('/api/register','AuthController@register');
$router->post('/api/login','AuthController@login');

$router->get('/api/posts',        'PostController@index');
$router->get('/api/posts/{id}',   'PostController@show');

$router->post('/api/posts', ['middleware' => 'jwt', 'uses' => 'PostController@store']);

$router->options('/{any:.*}', function () {
    return response('', 204);
});

$router->put('/api/posts/{id}',   ['middleware' => 'jwt', 'uses' => 'PostController@update']);
$router->delete('/api/posts/{id}',['middleware' => 'jwt', 'uses' => 'PostController@destroy']);

