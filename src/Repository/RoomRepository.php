<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 01:04
 */

namespace App\Repository;


use App\Entity\RoomEntity;
use JJWare\Util\Optional;

class RoomRepository extends AbstractRepository
{
    public function getRoom(int $roomId)
    {
        $st = $this->db->prepare('SELECT * FROM Room WHERE id=?');
        $st->execute([$roomId]);
        $res = $st->fetch();
        return $res ? Optional::of(RoomEntity::fromArray($res)) : Optional::empty();
    }
}