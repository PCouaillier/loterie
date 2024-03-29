<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 01:13
 */

namespace App\Controller;


use App\Entity\GiftEntity;
use App\Entity\UserEntity;
use App\Repository\GiftRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRoomRepository;
use App\Service\TransactionService;
use Exception;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class GiftController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function getGifts(Request $request, Response $response, int $roomId): ResponseInterface
    {
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        /** @var UserRoomRepository $userRoomRepository */
        $userRoomRepository = $this->container->get('UserRoomRepository');
        /** @var GiftRepository $giftRepository */
        $giftRepository = $this->container->get('GiftRepository');

        $roomOptional = $roomRepository->getRoom($roomId);
        if ($roomOptional->isPresent() === false) {
            return $this->container->get('notFoundHandler')($request, $response);
        }
        $room = $roomOptional->get();
        $owner = $userRoomRepository->getRoomOwner($roomId);
        return $this->view->render($response, 'Gift/gifts.html.twig', [
            'owner' => $owner,
            'room' => $room,
            'gifts' => $giftRepository->getGifts($roomId)
        ]);

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @return Response
     * @throws ContainerException
     */
    public function addGift(Request $request, Response $response, int $roomId): ResponseInterface
    {
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        /** @var UserRoomRepository $userRoomRepository */
        $userRoomRepository = $this->container->get('UserRoomRepository');
        $roomOptional = $roomRepository->getRoom($roomId);
        if ($roomOptional->isPresent() === false) {
            return $this->container->get('notFoundHandler')($request, $response);
        }
        $room = $roomOptional->get();
        $owner = $userRoomRepository->getRoomOwner($roomId);
        return $this->view->render($response, 'Gift/addGift.html.twig', [
            'owner' => $owner,
            'room' => $room
        ]);
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @return ResponseInterface
     * @throws ContainerException
     */
    public function addGiftPost(Request $request, Response $response, int $roomId): ResponseInterface
    {
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        /** @var UserRoomRepository $userRoomRepository */
        $userRoomRepository = $this->container->get('UserRoomRepository');
        /** @var GiftRepository $giftRepository */
        $giftRepository = $this->container->get('GiftRepository');

        $roomOptional = $roomRepository->getRoom($roomId);
        if ($roomOptional->isPresent() === false) {
            return $this->container->get('notFoundHandler')($request, $response);
        }
        $room = $roomOptional->get();
        $_POST['room'] = $roomId;
        $giftRepository->addGiftWithRoomId($roomId, GiftEntity::fromArray($_POST));
        $owner = $userRoomRepository->getRoomOwner($roomId);
        return $this->view->render($response, 'Gift/addGift.html.twig', [
            'owner' => $owner,
            'room' => $room
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param int $roomId
     * @param int $giftId
     * @return ResponseInterface
     * @throws ContainerException
     * @throws Exception
     */
    public function byGift(Request $request, Response $response, int $roomId, int $giftId): ResponseInterface
    {
        /** @var UserEntity $user */
        $user = $this->getUser()->get();
        /** @var TransactionService $transactionService */
        $transactionService = $this->container->get('TransactionService');
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->container->get('RoomRepository');
        /** @var GiftRepository $giftRepository */
        $giftRepository = $this->container->get('GiftRepository');

        $roomOptional = $roomRepository->getRoom($roomId);

        if ($roomOptional->isPresent() === false) {
            return $this->container->get('notFoundHandler')($request, $response);
        }
        $room = $roomOptional->get();

        $giftOptional = $giftRepository->getGiftById($room, $giftId);

        if ($giftOptional->isPresent() === false) {
            return $this->container->get('notFoundHandler')($response, $response);
        }
        $gift = $giftOptional->get();

        try {
            $transactionService->addTransaction($room, $user, $gift);
        }
        catch (Exception $exception) {
            return $response->write($exception->getMessage());
        }
        return $response->withRedirect('/room/'.$roomId.'/roll');
    }
}