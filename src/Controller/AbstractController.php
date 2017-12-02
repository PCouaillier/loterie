<?php
/**
 * Created by PhpStorm.
 * User: paulcouaillier
 * Date: 30/11/2017
 * Time: 00:01
 */

namespace App\Controller;


use App\Entity\UserEntity;
use Interop\Container\Exception\ContainerException;
use JJWare\Util\Optional;
use Slim\Container;
use Slim\Views\Twig;

abstract class AbstractController
{
    /** @var Container */
    protected $container;

    /** @var Twig */
    protected $view;

    /**
     * AbstractController constructor.
     * @param Container $c
     * @throws ContainerException
     */
    public function __construct(Container $c)
    {
        $this->container = $c;
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