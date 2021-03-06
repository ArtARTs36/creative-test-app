<?php
/**
 * 2019-06-13.
 */

declare(strict_types=1);

namespace App\Provider;

use App\Auth\Auth;
use App\Auth\Drivers\SessionDriver;
use App\Controller\HomeController;
use App\Controller\MovieController;
use App\Controller\UserController;
use App\Services\LikeService;
use App\Services\UserService;
use App\Support\Config;
use App\Support\ServiceProviderInterface;
use App\Support\Session;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use UltraLite\Container\Container;

/**
 * Class WebProvider.
 */
final class WebProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     * @return mixed|void
     */
    public function register(Container $container)
    {
        $this->defineControllerDi($container);
        $this->defineRoutes($container);
    }

    protected function defineControllerDi(Container $container): void
    {
        $container->set(UserService::class, static function (ContainerInterface $container) {
            return new UserService($container->get(EntityManagerInterface::class));
        });

        $container->set(Session::class, static function () {
            return new Session();
        });

        $container->set(Auth::class, static function (ContainerInterface $container) {
            return new Auth(
                new SessionDriver($container->get(Session::class)),
                $container->get(EntityManagerInterface::class)
            );
        });

        $container->set(LikeService::class, static function (ContainerInterface $container) {
            return new LikeService($container->get(EntityManagerInterface::class));
        });

        $this->registerControllers($container);
    }

    protected function registerControllers(ContainerInterface $container): void
    {
        $container->set(HomeController::class, static function (ContainerInterface $container) {
            return new HomeController(
                $container->get(Environment::class),
                $container->get(EntityManagerInterface::class),
                $container->get(Auth::class)
            );
        });

        $container->set(MovieController::class, static function (ContainerInterface $container) {
            return new MovieController(
                $container->get(EntityManagerInterface::class),
                $container->get(Environment::class),
                $container->get(Auth::class),
                $container->get(LikeService::class)
            );
        });

        $container->set(UserController::class, static function (ContainerInterface $container) {
            return new UserController(
                $container->get(UserService::class),
                $container->get(Environment::class),
                $container->get(Auth::class)
            );
        });
    }

    protected function defineRoutes(Container $container): void
    {
        $router = $container->get(RouteCollectorInterface::class);

        $router->group('/', function (RouteCollectorProxyInterface $router) use ($container) {
            foreach (self::getRoutes($container) as $routeName => $routeConfig) {
                $router
                    ->{$routeConfig['method']}(
                        $routeConfig['path'] ?? '',
                        $routeConfig['controller'] . ':' . $routeConfig['action']
                    )
                    ->setName($routeName);
            }
        });
    }

    protected static function getRoutes(Container $container): array
    {
        return Yaml::parseFile($container->get(Config::class)->get('base_dir') . '/config/routes.yaml');
    }
}
