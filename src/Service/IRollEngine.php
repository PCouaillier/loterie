<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 27/11/2017
 * Time: 16:42
 */

namespace App\Service;


interface IRollEngine
{
    function roll(): int;
}
