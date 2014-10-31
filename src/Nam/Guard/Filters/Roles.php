<?php


namespace Mbibi\Core\Http\Filters;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Log\Writer;
use Illuminate\Routing\Route;
use Monolog\Logger;


/**
 * Class Roles
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard\Filters
 *
 */
class Roles
{
    use AclFilterTrait;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->log = $app->make('log');
    }

    /**
     * @param Route      $route
     * @param Request    $request
     * @param mixed|null $value
     *
     * @return mixed
     */
    public function filter(Route $route, Request $request, $value = null)
    {
        /** @var Writer|Logger $log */
        $this->log = $this->app->make('log');

        if (is_null($value)) {
            $this->log->warning("Route [{$route->getName()}] declared \"roles\" filer but does not declare filter parameters.");

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