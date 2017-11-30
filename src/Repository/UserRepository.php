<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 28/11/2017
 * Time: 14:17
 */

namespace App\Repository;

// use Doctrine\ORM\EntityRepository;
use App\Entity\UserEntity;
use JJWare\Util\Optional;

class UserRepository extends AbstractRepository// extends EntityRepository
{
    /**
     * @param string $username
     * @param string $password
     * @return Optional Optional<UserEntity>
     */
    public function findByCredential(string $username, string $password): Optional
    {
        $st = $this->db->query('SELECT * FROM User');
        $userFound = $this->findByUserName($username);
        if ($userFound->isPresent() === false || password_verify($password , $userFound->get()['password']) === false) {
            return Optional::empty();
        }
        return $userFound->map(function ($a){ return UserEntity::fromArray($a); });
    }

    /**
     * Security issues : Return an optional that can be empty or containing an array extracted from the DataBase.
     *
     * @param string $username
     * @return Optional Optional<[string=> mixed]>
     */
    public function findByUserName(string $username): Optional
    {
        $st = $this->db->prepare('SELECT * FROM User WHERE username=? OR mail=?');
        $st->execute([$username, $username]);
        if ($res = $st->fetch()) {
            return Optional::of($res);
        }
        return Optional::empty();
    }

    public function findById(int $id): Optional
    {
        $st = $this->db->prepare('SELECT * FROM User WHERE id=?');
        $st->execute([$id]);
        return Optional::of(UserEntity::fromArray($st->fetch()));
    }

    public function createUser(UserEntity $user)
    {
        $db = $this->db;
        $db->beginTransaction();

        $st = $db->prepare('INSERT INTO `User` (username, displayName, mail, password) VALUES (? ,?, ?, ?)');
        $st->execute([$user->username, $user->displayName, $user->mail, $user->password]);
        $id = intval($db->lastInsertId());

        $this->db->commit();
        return $id;
    }
}