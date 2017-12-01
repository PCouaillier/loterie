<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 09:31
 */

namespace App\Entity;


class RollEntriesEntity
{
    public $room;
    public $user;
    public $date;
    public $points;

    public function __construct(string $room, string $user, \DateTime $date, int $points)
    {
        $this->room = $room;
        $this->user = $user;
        $this->date = $date;
        $this->points = $points;
    }

    public static function fromArray(array $a): RollEntriesEntity
    {
        return new RollEntriesEntity($a['room'], $a['user'], $a['date'], $a['points']);
    }
}