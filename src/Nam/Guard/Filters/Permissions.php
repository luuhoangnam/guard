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
    use ExtractRequirementsTrait;

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
     * @param Route   $route
     * @param Request $request
     * @param mixed   $parameters
     *
     * @return bool|mixed
     */
    public function filter(Route $route, Request $request, $parameters = null)
    {
        if (empty( $parameters )) {
            $this->log->warning("Route [{$route->getName()}] declared \"permissions\" filer but does not declare filter parameters.");

            return null; // Allow
        }

        $requirements = $this->extractRequirements($parameters);

        // Censor all access
        return $this->guard->censor($requirements);
    }

}