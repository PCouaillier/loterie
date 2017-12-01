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
     * @return Response
     * @throws ContainerException
     */
    public function addGift(Request $request, Response $response, int $roomId): ResponseInterface
    {
        /** @var GiftRepository $giftRepository */
        $giftRepository = $this->container->get('GiftRepository');
        $giftRepository->addGiftWithRoomId($roomId, GiftEntity::fromArray($_POST));
        return $this->renderer->render($response, 'Gift/AddGiftSuccess.phtml');
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

        $transactionService->addTransaction($room, $user, $gift);

        return $response->write('Hello');
    }
}