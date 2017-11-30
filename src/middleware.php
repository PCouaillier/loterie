<?php
// Application middleware

use Psr\Http\Message\RequestInterface;
use Slim\Http\Response;

if (!defined('USER_SESSION')) {
    define('USER_SESSION', 'USER_SESSION');
}

$redirectIfNotConnected = function (RequestInterface $request, Response $response, $next): Response {
    if (empty($_SESSION[USER_SESSION])) {
        return $response->withRedirect('/login');
    }
    $next($request, $response);
    return $response;
};
