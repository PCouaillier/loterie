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
use App\Repository\TransactionService;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class GiftController extends AbstractController
{
    /**
     * @param Request $request
     * @param Response $response
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

    public function byGift(Request $request, Response $response, int $roomId, int $giftId): ResponseInterface
    {
        /** @var UserEntity $user */
        $user = $this->getUser();
        /** @var TransactionService */
        $this->container->get('TransactionService');
    }
}