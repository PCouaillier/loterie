<?php

use App\Controller\DefaultController;
use App\Controller\GiftController;
use App\Controller\RoomController;
use App\Controller\UserController;
use Slim\Http\Request;
use Slim\Http\Response;

if (!defined('USER_SESSION')) {
    define('USER_SESSION', 'USER_SESSION');
}
// Routes

$app->get('/login', function (Request $request, Response $response) {
    return (new DefaultController($this))->loginGet($request, $response);
});

$app->post('/login', function (Request $request, Response $response) {
    return (new DefaultController($this))->loginPost($request, $response);
});

$app->get('/room/{roomId}', function (Request $request, Response $response, $args) {
    return (new RoomController($this))->getRoom($request, $response, intval($args['roomId']));
})->add($redirectIfNotConnected);

$app->get('/room/{roomId}/register', function (Request $request, Response $response, $args) {
    return (new UserController($this))->createUser($request, $response, $args['roomId']);
});

$app->post('/room/{roomId}/register', function (Request $request, Response $response, $args) {
    return (new UserController($this))->createUserPost($request, $response, $args['roomId']);
});

$app->get('/room/{roomId}/roll', function (Request $request, Response $response, $args) {
    return (new RoomController($this))->rollForRoom($request, $response, $args['roomId']);
})->add($redirectIfNotConnected);

$app->post('/room/{roomId}/roll', function (Request $request, Response $response, $args) {
    return (new RoomController($this))->rollForRoomPost($request, $response, $args['roomId']);
})->add($redirectIfNotConnected);

$app->get('/room/{roomId}/gift/add', function (Request $request, Response $response, $args) {
    return (new GiftController($this))->addGift($request, $response, intval($args['roomId']));
})->add($redirectIfNotConnected);

$app->get('/room/{roomId}/gift/{giftId}/buy', function (Request $request, Response $response, $args) {
    return (new GiftController($this))->byGift($request, $response, intval($args['roomId']), intval($args['giftId']));
})->add($redirectIfNotConnected);
