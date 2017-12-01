<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 10:33
 */

namespace App\Entity;


class GiftEntity
{
    public $id;
    public $room;
    public $quantity;
    public $cost;
    public $name;

    public function __construct(int $room, int $quantity,int $cost, string $name ,int $id = 0)
    {
        $this->id = $id;
        $this->room = $room;
        $this->quantity = $quantity;
        $this->cost = $cost;
        $this->name = $name;
    }

    public static function fromArray(array $a): GiftEntity
    {
        return new GiftEntity(intval($a['room']), intval($a['quantity']), intval($a['cost']), $a['name'], intval($a['id'] ?? 0));
    }
}