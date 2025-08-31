<?php

$router->get('/', fn() => response()->json(['ok'=>true,'service'=>'lumen-api'] ));
$router->get('/ping', fn() => response('pong',200));

$router->post('/api/register','AuthController@register');
$router->post('/api/login','AuthController@login');
