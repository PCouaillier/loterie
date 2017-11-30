<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 27/11/2017
 * Time: 16:41
 */

namespace App\Repository;

use App\Entity\RollEntriesEntity;
use App\Entity\RoomEntity;
use App\Entity\UserEntity;
// use Doctrine\ORM\EntityRepository;
use JJWare\Util\Optional;

class PointsRepository extends AbstractRepository// extends EntityRepository
{

    public function userLastEntry(UserEntity $user, RoomEntity $room): Optional
    {
        return $this->userLastEntrySqlite($user->id, $room->id)->map(function ($a){
            $a['date'] = new \DateTime($a['date']);
            return RollEntriesEntity::fromArray($a);
        });
    }

    public function addNewEntry(UserEntity $user, RoomEntity $room, \DateTime $date, int $points): bool
    {
        return $this->addNewEntryWithIds($user->id, $room->id, $date, $points);
    }

    private function userLastEntrySqlite(int $userId, int $roomId): Optional
    {
        $st = $this->db->prepare('SELECT * FROM UserRollInRoom WHERE `user`=:user AND `room`=:room ORDER BY `date` DESC LIMIT 1');
        $st->bindParam(':user', $userId, \PDO::PARAM_INT);
        $st->bindParam(':room', $roomId, \PDO::PARAM_INT);
        $st->execute();
        $last = $st->fetch();
        return $last ? Optional::of($last) : Optional::empty();
    }

    public function addNewEntryWithIds(int $user, int $room, \DateTime $date, int $points): bool
    {
        $st = $this->db->prepare('INSERT INTO UserRollInRoom (user, room, date, points) VALUES (?, ?, ?, ?);');
        return $st->execute([$user, $room, $date->format('Y-m-d H:i:s'), $points]);
    }
}
