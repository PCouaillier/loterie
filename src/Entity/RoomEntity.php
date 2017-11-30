<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 28/11/2017
 * Time: 15:16
 */

namespace App\Entity;
use DateTime;

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

    /** @var ?DateTime */
    public $begin;

    /** @var ?DateTime */
    public $end;

    public function __construct(int $owner, $engine, DateTime $begin, DateTime $end, int $id)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->engine = $engine;
        $this->begin = $begin;
        $this->end = $end;
    }

    public static function fromArray(array $room): RoomEntity
    {
        return new RoomEntity(intval($room['owner']), $room['engine'], self::castDateTime($room['begin']), self::castDateTime($room['end']), intval($room['id'] ?? 0));
    }
    
    public static function castDateTime($d): ?DateTime
    {
        return $d ? new DateTime($d) : null;
    }
}