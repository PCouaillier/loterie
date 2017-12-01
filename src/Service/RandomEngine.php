<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 27/11/2017
 * Time: 17:08
 */

namespace App\Service;

class RandomEngine implements IRollEngine
{
    private $min;
    private $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return int
     * @throws \Exception
     */
    function roll(): int
    {
        return random_int($this->min, $this->max);
    }
}
