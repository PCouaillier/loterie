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
use JJWare\Util\Optional;

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

    public function getGiftById(RoomEntity $room, int $giftId): Optional
    {
        $st = $this->db->prepare('SELECT * FROM Gift WHERE id=? AND room=?;');
        $st->execute([$giftId, $room->id]);
        $res = $st->fetch();
        return $res ? Optional::of(GiftEntity::fromArray($res)) : Optional::empty();
    }

    public function getGifts(int $roomId): array
    {
        $st = $this->db->prepare('SELECT * FROM Gift WHERE room=?');
        $st->execute([$roomId]);
        $res = $st->fetchAll();
        return array_map(function ($gift) { return GiftEntity::fromArray($gift); }, $res);
    }
}