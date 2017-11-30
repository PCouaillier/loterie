<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 29/11/2017
 * Time: 23:58
 */

namespace App\Controller;

use App\Repository\UserRoomRepository;
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
        session_destroy();
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
        session_destroy();
        session_start();
        if (!empty($_POST) && !empty($_POST['username'])) {
            /** @var \App\Repository\UserRepository $userRepository */
            $userRepository = $this->container->get('UserRepository');

            $userOptional = $userRepository ->findByCredential($_POST['username'], $_POST['password']);

            if ($userOptional->isPresent()) {
                $user = $userOptional->get();
                $_SESSION[USER_SESSION] = $user;
                $this->container->get('logger')->info('User connected : '.$user->username);

                /** @var UserRoomRepository $userRoomRepository */
                $userRoomRepository = $this->container->get('UserRoomRepository');
                $rooms = $userRoomRepository->getAccessibleRooms($user);
                if (empty($rooms)) {
                    return $response->withRedirect('/');
                }
                return $response->withRedirect('/room/'.$rooms[0]->id.'/roll');
            }
        }
        return $this->renderer->render($response, 'login.phtml', []);
    }
}