<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 16:54
 */

namespace App\Controller;

use App\Entity\UserEntity;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Repository\UserRoomRepository;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function createUser(Request $request, Response $response, int $roomId): ResponseInterface
    {
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        /** @var UserRoomRepository $roomRepository */
        $userRoomRepository = $this->container->get('UserRoomRepository');

        $roomOptional = $roomRepository->getRoom($roomId);
        if ($roomOptional->isPresent() === false) {
            return $response->withStatus(404);
        }
        $room = $roomOptional->get();
        $owner = $userRoomRepository->getRoomOwner($room);

        return $this->view->render($response, 'User/create.twig.html', ['owner' => $owner]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function createUserPost(Request $request, Response $response, int $roomId): ResponseInterface
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('UserRepository');
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        /** @var UserRoomRepository $userRoomRepository */
        $userRoomRepository = $this->container->get('UserRoomRepository');

        $roomOptional = $roomRepository->getRoom($roomId);
        if ($roomOptional->isPresent() === false) {
            return $response->withStatus(404);
        }

        $userId = $userRepository->createUser(UserEntity::fromArray($_POST));

        $userRoomRepository->addUserToRoomByIds($userId, $roomId);

        return $response->withRedirect('/room/'.$roomId.'/roll');
    }
}