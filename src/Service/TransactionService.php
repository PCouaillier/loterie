<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 12:20
 */

namespace App\Service;

use App\Entity\GiftEntity;
use App\Entity\RoomEntity;
use App\Entity\UserEntity;
use Exception;
use JJWare\Util\Optional;
use Monolog\Logger;
use PDO;

class TransactionService
{
    private $db;
    private $log;

    public function __construct(PDO $db, Logger $log)
    {
        $this->db = $db;
        $this->log = $log;
    }

    public function getBalance(RoomEntity $room, UserEntity $user): Optional
    {
        $sumOfRolls = $this->getSumOfRolls($room->id, $user->id);
        if ($sumOfRolls->isPresent() === false) {
            return Optional::empty();
        }
        $sumOfTransaction = $this->getSumOfTransaction($room->id, $user->id);
        if ($sumOfTransaction->isPresent() === false) {
            return Optional::empty();
        }
        return Optional::of($sumOfRolls->get() - $sumOfTransaction->get());
    }

    private function getSumOfRolls(int $roomId, int $userId): Optional
    {
        $st = $this->db->prepare('SELECT SUM(points) as "balance" FROM `UserRollInRoom` WHERE `room`=:room AND `user`=:user;');
        $st->bindParam(':room', $roomId, PDO::PARAM_INT);
        $st->bindParam(':user', $userId, PDO::PARAM_INT);
        $st->execute();
        $balance = $st->fetch();
        return $balance ? Optional::of(intval($balance['balance'])) : Optional::empty();
    }

    private function getSumOfTransaction(int $roomId, int $userId): Optional
    {
        $st = $this->db->prepare('SELECT SUM(cost) as "total" FROM `Transaction` WHERE `room`=:room AND `user`=:user;');
        $st->bindParam(':room', $roomId, PDO::PARAM_INT);
        $st->bindParam(':user', $userId, PDO::PARAM_INT);
        $st->execute();
        $total = $st->fetch();
        return $total ? Optional::of(intval($total['total'])) : Optional::empty();
    }

    /**
     * @param RoomEntity $room
     * @param UserEntity $user
     * @param GiftEntity $gift
     * @throws Exception
     */
    public function addTransaction(RoomEntity $room, UserEntity $user, GiftEntity $gift)
    {
        $balance = $this->getBalance($room, $user)->orElse(0);
        if ($balance < $gift->cost) {
            throw new Exception('Not Enough currency');
        }
        $st = $this->db->prepare('INSERT INTO `Transaction` (`room`, `user`, `gift`, `date`, `cost`) VALUES (:room, :user, :gift, :date, :cost)');
        $st->bindParam(':room', $room->id, PDO::PARAM_INT);
        $st->bindParam(':user', $user->id, PDO::PARAM_INT);
        $st->bindParam(':gift', $gift->id, PDO::PARAM_INT);
        $st->bindValue(':date', (new \DateTime())->format('Y-m-d h:i:s'), PDO::PARAM_STR);
        $st->bindParam(':cost', $gift->cost, PDO::PARAM_INT);
        $st->execute();
    }
}
