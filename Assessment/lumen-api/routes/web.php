<?php

$router->get('/', function () {
    return response()->json(['ok' => true, 'service' => 'lumen-api']);
});

$router->get('/ping', function () {
    return response('pong', 200);
});
