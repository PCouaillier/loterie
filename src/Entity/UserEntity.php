<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 28/11/2017
 * Time: 10:21
 */

namespace App\Entity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class UserEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    public $username;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    public $displayName;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    public $mail;

    /**
     * @ORM\Column(type="string", scale=2, nullable=false)
     * @var string
     */
    public $password;

    /**
     * @ORM\Column(type="bool", nullable=false, default=false)
     * @var string
     */
    public $canAddRoom;

    public function __construct($id, string $username, string $mail, string $password, string $displayName, bool $canAddRoom=false)
    {
        $this->id = $id;
        $this->username = $username;
        $this->mail = $mail;
        $this->password = $password;
        $this->displayName = $displayName;
        $this->canAddRoom = $canAddRoom;
    }

    public static function fromArray(array $array)
    {
        return new UserEntity($array['id'], $array['username'], $array['mail'], $array['password'], $array['displayName']?? $array['username'], $array['canAddRoom'] ?? false);
    }
}
