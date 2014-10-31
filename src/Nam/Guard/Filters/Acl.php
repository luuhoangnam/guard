<?php


namespace Nam\Guard\Filters;

use Illuminate\Foundation\Application;
use Illuminate\Log\Writer;
use Illuminate\Routing\Route;
use Monolog\Logger;
use Nam\Guard\Guard;
use ReflectionClass;


/**
 * Class Acl
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard\Filters
 *
 */
class Acl extends Guard
{
    /**
     * @var Guard
     */
    protected $guard;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Writer|Logger
     */
    protected $log;

    /**
     * @param Application $app
     * @param Guard       $guard
     */
    public function __construct(Application $app, Guard $guard)
    {
        $this->guard = $guard;
        $this->app = $app;
        $this->log = $this->app->make('log');
    }

    /**
     * @param Route $route
     *
     * @return bool|mixed
     */
    public function filter(Route $route)
    {
        if ($route->getActionName() === 'Closure') {
            $this->log->warning("Register \"acl\" filter on route [{$route->getName()}] uses Closure action.");

            return null; // Allow
        }

        $requirements = $this->extractRequirements($route);

        // Censor all access
        return $this->guard->censor($requirements);
    }

    /**
     *
     * @param Route $route
     *
     * @return array
     */
    protected function extractRequirements(Route $route)
    {
        $docComment = $this->getDocComment($route->getAction()['controller']);

        $requirements = [
            'permissions' => [ ],
            'roles'       => [ ],
        ];

        if (isset( $docComment['permissions'] )) {
            $requirements['permissions'] = explode('|', $docComment['permissions']);
        }

        if (isset( $docComment['roles'] )) {
            $requirements['roles'] = explode('|', $docComment['roles']);
        }

        return $requirements;
    }

    /**
     * @param string $action
     *
     * @return array
     */
    protected function getDocComment($action)
    {
        $segments = explode('@', $action);
        $controller = $segments[0];
        $action = $segments[1];

        $reflection = new ReflectionClass($controller);
        $actionMethod = $reflection->getMethod($action);
        $docComment = $actionMethod->getDocComment();

        $result = [ ];

        if (preg_match_all('/@(\w+)\s+(.*)\r?\n/m', $docComment, $matches)) {
            $result = array_combine($matches[1], $matches[2]);
        }

        return $result;
    }

}