<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 29/11/2017
 * Time: 23:58
 */

namespace App\Controller;

use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class DefaultController extends AbstractController
{
    /**
     * @param Response $response
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function index(Response $response): ResponseInterface
    {
        return $this->renderer->render($response, 'index.phtml', []);
    }

    /**
     * @param Response $response
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function loginGet(Request $request, Response $response): ResponseInterface
    {
        session_reset();
        $user = $this->getUser();
        return $this->renderer->render($response, 'login.phtml', ['Debug' => var_export($_POST), 'User'=> $user->orElse(null)]);
    }

    /**
     * @param Response $response
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function loginPost(Request $request, Response $response): ResponseInterface
    {
        session_reset();
        if (!empty($_POST) && !empty($_POST['username'])) {
            /** @var \App\Repository\UserRepository $userRepository */
            $userRepository = $this->container->get('UserRepository');

            $user = $userRepository ->findByCredential($_POST['username'], $_POST['password']);

            if ($user->isPresent()) {
                $_SESSION[USER_SESSION] = $user->get();
                $this->container->get('logger')->info('User connected : '.$user->get()->username);
                return $response->withRedirect('/');
            }
        }
        return $this->renderer->render($response, 'login.phtml', []);
    }
}