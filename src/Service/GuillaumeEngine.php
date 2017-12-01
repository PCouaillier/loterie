<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 12:41
 */

namespace App\Service;


class GuillaumeEngine implements IRollEngine
{
    /**
     * @return int
     * @throws \Exception
     */
    function roll(): int
    {
        $n = random_int(0, 99);
        if ($n < 10) {
            return 0;
        } else if ($n < 50) {
            return 1;
        } else if ($n < 80) {
            return 3;
        }
        return 5;
    }
}