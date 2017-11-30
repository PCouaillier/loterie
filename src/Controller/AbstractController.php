<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 00:01
 */

namespace App\Controller;


use App\Entity\UserEntity;
use JJWare\Util\Optional;
use Slim\Container;
use Slim\Views\PhpRenderer;
use Slim\Views\Twig;

abstract class AbstractController
{
    /** @var Container */
    protected $container;

    /** @var PhpRenderer */
    protected $renderer;

    /** @var Twig */
    protected $view;

    public function __construct(Container $c)
    {
        $this->container = $c;
        $this->renderer = $c->get('renderer');
        $this->view = $c->get('view');
    }

    /**
     * @return Optional
     */
    protected function getUser(): Optional
    {
        if(empty($_SESSION) || empty($_SESSION[USER_SESSION])) {
            return Optional::empty();
        }
        $user = $_SESSION[USER_SESSION];
        if ($user instanceOf UserEntity) {
            return Optional::ofNullable($user);
        }
        return Optional::empty();
    }
}