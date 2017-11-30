<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 28/11/2017
 * Time: 15:16
 */

namespace App\Entity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 */
class RoomEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    public $id;

    public $owner;

    /**
     * @ORM\Column(type="string", length=15)
     * @var string
     */
    public $engine;

    public function __construct(int $id, int $owner, $engine)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->engine = $engine;
    }

    public static function fromArray(array $room): RoomEntity
    {
        return new RoomEntity(intval($room['id'] ?? 0), intval($room['owner']), $room['engine']);
    }
}