<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 24/11/2017
 * Time: 22:29
 */

namespace App\Service;

use App\Entity\RoomEntity;
use App\Entity\UserEntity;
use App\Repository\PointsRepository;
use DateTime;
use JJWare\Util\Optional;

class PointGenerator
{
    private $repo;
    private $rollEngine;

    public function __construct(PointsRepository $repo, IRollEngine $rollEngine)
    {
        $this->repo = $repo;
        $this->rollEngine = $rollEngine;
    }

    public function userRollPoints(UserEntity $user, RoomEntity $room): Optional
    {
        if ($user != null && $this->canUserRoll($user, $room)) {
            $rollValue = $this->rollEngine->roll();
            $this->repo->addNewEntry($user, $room, (new DateTime())->setTime(0, 0, 0), $rollValue);
            return Optional::of($rollValue);
        }
        return Optional::empty();
    }

    private function canUserRoll(UserEntity $user, RoomEntity $room): bool
    {
        $today = (new DateTime())->setTime(0, 0, 0);
        if ($this->isActive($room, $today) === false) {
            return false;
        }
        return $this->repo
            ->UserLastEntry($user, $room)
            ->map(function ($entry) use ($today) {
                return $this->isSameDate($entry->date, $today) === false;
            })
            ->orElse(true)
        ;
    }

    private function isSameDate(DateTime $d1, DateTime $d2): bool
    {
        return $d1->diff($d2)->days < 1;
    }

    private function isActive(RoomEntity $room, DateTime $date): bool
    {
        return ($room->begin == null || $room->begin <= $date) && ($room->end == null || $date <= $room->end);
    }
}
