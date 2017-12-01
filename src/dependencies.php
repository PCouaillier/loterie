<?php
// DIC configuration
use App\Factory\EngineFactory;
use App\Repository\GiftRepository;
use App\Repository\PointsRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Repository\UserRoomRepository;
use App\Service\SqliteDbCreator;
use App\Service\TransactionService;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Slim\Views\PhpRenderer;

define('USING_SQLITE', true);

$container = $app->getContainer();

// view renderer
$container['renderer'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['renderer'];
    return new PhpRenderer($settings['template_path']);
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'templates');/*, [
        'cache' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache'
    ]);*/
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

// monolog
$container['logger'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new UidProcessor());
    $logger->pushHandler(new StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['Db'] = new class {
    private $instance;
    public function __construct()
    {
        $this->instance = new class extends PDO
        {
            public function __construct()
            {
                parent::__construct('sqlite:'.__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'db.sqlite3');
                if (USING_SQLITE) {
                    (new SqliteDbCreator($this))->createSqliteDb();
                }
            }
        };
    }
    public function __invoke()
    {
        return $this->instance;
    }
};

$container['GiftsRepository'] = function (ContainerInterface $c) {
    return new GiftRepository($c->get('Db'), $c->get('logger'));
};

$container['PointsRepository'] = function (ContainerInterface $c) {
    return new PointsRepository($c->get('Db'), $c->get('logger'));
};

$container['RoomRepository'] = function (ContainerInterface $c) {
    return new RoomRepository($c->get('Db'), $c->get('logger'));
};

$container['UserRepository'] = function (ContainerInterface $c) {
    return new UserRepository($c->get('Db'), $c->get('logger'));
};

$container['UserRoomRepository'] = function (ContainerInterface $c) {
    return new UserRoomRepository($c->get('Db'), $c->get('logger'));
};

$container['EngineFactory'] = new class {
    private $engineFactory;
    public function __construct()
    {
        $this->engineFactory = new EngineFactory();
    }
    public function __invoke() {
        return $this->engineFactory;
    }
};

$container['TransactionService'] = function (ContainerInterface $c) {
    return new TransactionService($c->get('Db'), $c->get('logger'));
};