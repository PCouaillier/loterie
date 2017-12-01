<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 00:18
 */

namespace App\Controller;

use App\Entity\RoomEntity;
use App\Entity\UserEntity;
use App\Factory\EngineFactory;
use App\Repository\PointsRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Service\PointGenerator;
use App\Service\RandomEngine;
use App\Service\TransactionService;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class RoomController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function roomIndex(Request $request, Response $response): ResponseInterface
    {
        /** @var \PDO $db */
        $db = $this->container->get('Db');
        $rooms = $db->query('SELECT * FROM `Room`;')->fetchAll();
        return $this->renderer->render($response, 'Room/roomIndex.phtml', ['rooms' => $rooms]);
    }

    public function addRoomGet(Request $request, Response $response): ResponseInterface
    {
        return $this->renderer->render($response, 'roomAdd.phtml');
    }

    public function addRoomPost(Request $request, Response $response): ResponseInterface
    {
        /** @var UserEntity $user */
        $user = $this->getUser()->get();
        if ($user->canAddRoom) {
            $a = $_POST;
            $room = RoomEntity::fromArray($a);
        }

        return $response->withRedirect('/room/'.$room->id);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function getRoom(Request $request, Response $response, int $roomId): ResponseInterface
    {
        $this->container->get('logger')->info("Controller::GetRoom roomId=$roomId");

        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        $room = $roomRepository->getRoom($roomId)->get();

        /** @var UserRepository $roomRepository */
        $userRepository = $this->container->get('UserRepository');
        $owner = $userRepository->findById($room->owner)->get();

        return $this->renderer->render($response, 'Room/roomMenu.phtml', ['owner' => $owner]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @return ResponseInterface
     * @throws ContainerException
     * @throws \Exception
     */
    public function rollForRoom(Request $request, Response $response, int $roomId): ResponseInterface
    {
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        /** @var EngineFactory $engineFactory */
        $engineFactory = $this->container->get('EngineFactory');
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('UserRepository');
        /** @var TransactionService $transactionService */
        $transactionService = $this->container->get('TransactionService');

        $user = $this->getUser()->get();

        $roomOptional = $roomRepository->getRoom($roomId);
        if ($roomOptional->isPresent() === false) {
            $response->isNotFound();
            return $response;
        }
        /** @var RoomEntity $room */
        $room = $roomOptional->get();

        $roomOwner = $userRepository->findById($room->owner)->get();
        return $this->view->render($response, 'Roll/roll.twig.html', [
                'owner' => $roomOwner,
                'balance' => $transactionService->getBalance($room, $user)->orElse(0),
                'rolling'    => true,
                'rollPoints' => null,
                'alreadyRolled' => null
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @return ResponseInterface
     * @throws ContainerException
     * @throws \Exception
     */
    public function rollForRoomPost(Request $request, Response $response, int $roomId): ResponseInterface
    {
        $user = $this->getUser()->get();

        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        /** @var PointsRepository $pointsRepository */
        $pointsRepository = $this->container->get('PointsRepository');
        /** @var EngineFactory $engineFactory */
        $engineFactory = $this->container->get('EngineFactory');
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get('UserRepository');
        /** @var TransactionService $transactionService */
        $transactionService = $this->container->get('TransactionService');

        $roomOptional = $roomRepository->getRoom($roomId);
        if ($roomOptional->isPresent() === false) {
            $response->isNotFound();
            return $response;
        }
        /** @var RoomEntity $room */
        $room = $roomOptional->get();
        $engine = $engineFactory->engine($room->engine);
        $roomOwner = $userRepository->findById($room->owner)->get();

        $pointGenerator = new PointGenerator($pointsRepository, $engine);
        $rollPoints = $pointGenerator->userRollPoints($user, $room);

        return $this->view->render($response, 'Roll/roll.twig.html', [
            'owner'         => $roomOwner,
            'balance'       => $transactionService->getBalance($room, $user)->orElse(0),
            'rollPoints'    => $rollPoints->orElse(null),
            'alreadyRolled' => !$rollPoints->isPresent()
        ]);
    }
}