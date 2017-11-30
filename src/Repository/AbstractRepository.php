<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 10:37
 */

namespace App\Repository;


use Monolog\Logger;

abstract class AbstractRepository
{
    /** @var \PDO */
    protected $db;

    /** @var Logger */
    protected $log;

    public function __construct($db, $log = null)
    {
        $this->db = $db;
        $this->log = $log;
    }
}