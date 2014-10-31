<?php


namespace Nam\Guard;

use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Guard as Auth;
use Illuminate\Log\Writer;
use Monolog\Logger;
use Nam\Guard\Exceptions\UnauthorizedException;


/**
 * Class HttpFilter
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard\Filters
 *
 */
class Guard
{
    /**
     * @var Writer|Logger
     */
    protected $log;

    /**
     * @var AuthManager|Auth
     */
    private $auth;

    /**
     * @param AuthManager $auth
     * @param Writer      $log
     */
    public function __construct(AuthManager $auth, Writer $log)
    {
        $this->log = $log;
        $this->auth = $auth;
    }

    /**
     * @param array $requirements
     *
     * @throws UnauthorizedException
     * @return mixed
     */
    public function censor(array $requirements = [ ])
    {
        if (empty( $requirements )) {
            return null;
        }
        
        $roles = $requirements['roles'] ?: [ ];
        $permissions = $requirements['permissions'] ?: [ ];

        // Allow if have no restrictions
        if (empty ( $roles ) && empty( $permissions )) {
            return; // Allow
        }

        // Otherwise deny guest & all unauthorized access
        $this->denyGuest($roles, $permissions);

        // Get current user & check
        /** @var Visitor $visitor */
        $visitor = $this->auth->user();
        $this->checkVisitorHasRoles($visitor, $roles);
        $this->checkVisitorHasPermissions($visitor, $permissions);
    }

    /**
     * @param Visitor $user
     * @param array   $roles
     *
     * @throws UnauthorizedException
     */
    private function checkVisitorHasRoles(Visitor $user, array $roles)
    {
        foreach ($roles as $role) {
            if ( ! $user->hasRole($role)) {
                throw new UnauthorizedException;
            }
        }
    }

    /**
     * @param Visitor $visitor
     * @param array   $permissions
     *
     * @throws UnauthorizedException
     */
    private function checkVisitorHasPermissions(Visitor $visitor, array $permissions)
    {
        foreach ($permissions as $permission) {
            if ( ! $visitor->can($permission)) {
                throw new UnauthorizedException;
            }
        }
    }

    /**
     * @throws UnauthorizedException
     */
    protected function denyGuest()
    {
        if ($this->auth->guest()) {
            throw new UnauthorizedException;
        }
    }
}