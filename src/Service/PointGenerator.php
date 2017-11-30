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
use JJWare\Util\Optional;

class PointGenerator
{
    private $repo;
    private $rollEngine;

    public function __construct(PointsRepository $repo, IRollEngine $rollEngine) {
        $this->repo = $repo;
        $this->rollEngine = $rollEngine;
    }

    public function userRollPoints(UserEntity $user, RoomEntity $room): Optional
    {
        if ($user!=null && $this->canUserRoll($user, $room)) {
            $rollValue = $this->rollEngine->roll();
            $this->repo->addNewEntry($user, $room, (new \DateTime())->setTime( 0, 0, 0), $rollValue);
            return Optional::of($rollValue);
        }
        return Optional::empty();
    }

    private function canUserRoll(UserEntity $user, RoomEntity $room): bool {
        return $this->repo
            ->UserLastEntry($user, $room)
            ->map(function ($entry) {
                return $this->isSameDate($entry->date, (new \DateTime())->setTime( 0, 0, 0));
            })
            ->orElse(true)
        ;
    }

    private function isSameDate(\DateTime $d1, \DateTime $d2): bool {
        return $d1 === $d2;
    }
}
