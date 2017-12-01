<?php

use Slim\Http\Request;
use Slim\Http\Response;

if (!defined('USER_SESSION')) {
    define('USER_SESSION', 'USER_SESSION');
}
// Routes

$app->get('/login', function (Request $request, Response $response) {
    return (new App\Controller\DefaultController($this))->loginGet($request, $response);
});

$app->post('/login', function (Request $request, Response $response) {
    return (new App\Controller\DefaultController($this))->loginPost($request, $response);
});

$app->get('/room/{roomId}', function (Request $request, Response $response, $args) {
    return (new App\Controller\RoomController($this))->getRoom($request, $response, $args['roomId']);
})->add($redirectIfNotConnected);

$app->get('/room/{roomId}/register', function (Request $request, Response $response, $args) {
    return (new App\Controller\UserController($this))->createUser($request, $response, $args['roomId']);
});

$app->post('/room/{roomId}/register', function (Request $request, Response $response, $args) {
    return (new App\Controller\UserController($this))->createUserPost($request, $response, $args['roomId']);
});

$app->get('/room/{roomId}/roll', function (Request $request, Response $response, $args) {
    return (new App\Controller\RoomController($this))->rollForRoom($request, $response, $args['roomId']);
})->add($redirectIfNotConnected);

$app->post('/room/{roomId}/roll', function (Request $request, Response $response, $args) {
    return (new App\Controller\RoomController($this))->rollForRoomPost($request, $response, $args['roomId']);
})->add($redirectIfNotConnected);
