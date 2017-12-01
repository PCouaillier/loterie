<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 27/11/2017
 * Time: 20:56
 */

namespace App\Repository;

//use Doctrine\ORM\EntityRepository;
use App\Entity\RoomEntity;
use App\Entity\UserEntity;
use JJWare\Util\Optional;
use PDO;

class UserRoomRepository extends AbstractRepository// extends EntityRepository
{
    /**
     * @param int $userId
     * @return array RoomEntity[]
     */
    public function getOwnedRooms(int $userId): array
    {
        $st = $this->db->prepare('SELECT * FROM Room WHERE owner=:owner;');
        $st->bindParam(':owner', $userId, PDO::PARAM_INT);
        $st->execute();
        $rooms = $st->fetchAll();
        return array_map(function($room){return RoomEntity::fromArray($room); }, $rooms);
    }

    /**
     * @param int $roomId
     * @return UserEntity|null
     */
    public function getRoomOwner(int $roomId): ?UserEntity
    {
        $st = $this->db->prepare('SELECT User.* FROM `Room` INNER JOIN User ON Room.owner=User.id WHERE Room.id=:roomId');
        $st->bindParam(':roomId', $roomId, PDO::PARAM_INT);
        $st->execute();
        $user = $st->fetch();
        return $user ? UserEntity::fromArray($user) : null;
    }

    /**
     * @param int $roomId
     * @return array UserEntity[]
     */
    private function findUsersInRoom(int $roomId): array
    {
        $st = $this->db->prepare('SELECT User.* FROM Room INNER JOIN UserInRoom ON UserInRoom.room=Room.id INNER JOIN User ON UserInRoom.user=User.id WHERE Room.id=?;');
        $st->execute([$roomId]);
        $users = $st->fetchAll();
        return $users ? array_walk($users, function($user) { return UserEntity::fromArray($user); }) : [];
    }

    public function addUserToRoom(UserEntity $user, RoomEntity $room) {
        return $this->addUserToRoomByIds($user->id, $room->id);
    }

    public function addUserToRoomByIds(int $userId, int $roomId)
    {
        $st = $this->db->prepare('INSERT INTO `UserInRoom` (user, room) VALUES (:userId, :roomId);');
        $st->bindParam(':userId', $userId, PDO::PARAM_INT);
        $st->bindParam(':roomId', $roomId, PDO::PARAM_INT);
        $st->execute();
    }

    public function getAccessibleRooms(UserEntity $user): array
    {
        $this->log->debug('user : '.$user->id);
        $st = $this->db->prepare('SELECT Room.* FROM UserInRoom INNER JOIN Room ON UserInRoom.room=Room.id WHERE UserInRoom.user=:user;');
        $st->bindParam(':user', $user->id, PDO::PARAM_INT);
        $st->execute();
        $rooms = $st->fetchAll();
        return array_merge(array_map(function($a) { return RoomEntity::fromArray($a); }, $rooms), $this->getOwnedRooms($user->id));
    }
}
