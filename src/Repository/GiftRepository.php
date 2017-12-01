<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 10:35
 */

namespace App\Repository;


use App\Entity\GiftEntity;
use App\Entity\RoomEntity;

class GiftRepository extends AbstractRepository
{

    /**
     * @param RoomEntity $room
     * @param GiftEntity $gift
     * @return int created Id
     */
    public function addGift(RoomEntity $room, GiftEntity $gift): int
    {
        return $this->addGiftWithRoomId($room->id, $gift);
    }

    /**
     * @param int $roomId
     * @param GiftEntity $gift
     * @return int created Id
     */
    public function addGiftWithRoomId(int $roomId, GiftEntity $gift): int
    {
        $st = $this->db->prepare('INSERT INTO Gift (`name`, `cost`, `quantity`, `room`) VALUES (?, ?, ?, ?);');
        $st->execute([$gift->name, $gift->cost, $gift->quantity, $gift->room]);
        return intval($this->db->lastInsertId());
    }
}