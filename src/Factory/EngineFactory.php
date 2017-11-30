<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 29/11/2017
 * Time: 11:51
 */

namespace App\Factory;

use App\Service\GuillaumeEngine;
use App\Service\IRollEngine;
use App\Service\RandomEngine;

class EngineFactory
{
    /**
     * @param string $engineName
     * @return IRollEngine
     * @throws \Exception
     */
    public function engine(string $engineName): IRollEngine
    {
        $match = null;
        if (strtolower($engineName) === 'guillaumeengine') {
            return new GuillaumeEngine();
        }
        else if (preg_match('/^(random)(\(([0-9]+),([0-9]+)\))?$/', $engineName, $matches)) {
            return $this->newRandomEngine($matches);
        }
        throw new \Exception('Bad Engine');
    }

    private function newRandomEngine(array &$matches): RandomEngine
    {
        if (count($matches)==2) {
            return new RandomEngine(2, 9);
        }
        else if (count($matches)==5) {
            return new RandomEngine(intval($matches[3]), intval($matches[4]));
        }
    }
}