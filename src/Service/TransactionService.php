<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 12:20
 */

namespace App\Service;

use App\Entity\RoomEntity;
use App\Entity\UserEntity;
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
        $st = $this->db->prepare('SELECT SUM(points) as "balance" FROM `UserRollInRoom` WHERE `room`=:room AND `user`=:user;');
        $st->bindParam(':room', $room->id, PDO::PARAM_INT);
        $st->bindParam(':user', $user->id, PDO::PARAM_INT);
        $st->execute();
        $balance = $st->fetch();
        return $balance ? Optional::of(intval($balance['balance'])) : Optional::empty();
    }
}
