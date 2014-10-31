<?php


namespace Nam\Guard\Filters;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Log\Writer;
use Illuminate\Routing\Route;
use Monolog\Logger;
use Nam\Guard\Guard;


/**
 * Class Permissions
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard\Filters
 *
 */
class Permissions
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var Application
     */
    private $app;

    /**
     * @var Writer|Logger
     */
    private $log;

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
     * @param Route      $route
     * @param Request    $request
     * @param mixed|null $value
     *
     * @return mixed|null
     */
    public function filter(Route $route, Request $request, $value = null)
    {
        if (is_null($value)) {
            $this->log->warning("Route [{$route->getName()}] declared \"permissions\" filer but does not declare filter parameters.");

            return null; // Allow
        }

        $acl = [
            'roles'       => [ ],
            'permissions' => [ ],
        ];

        $roles = explode('+', $value);

        foreach ($roles as $role) {
            $acl['roles'][] = trim($role);
        }

        return $this->guard($acl, 'roles');
    }
}