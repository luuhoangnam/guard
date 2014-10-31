<?php


namespace Nam\Guard\TestCases;

use Mockery;
use Nam\Guard\Guard;


/**
 * GuardTest Test Case
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard\TestCases
 *
 */
class GuardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Nam\Guard\Exceptions\UnauthorizedException
     */
    public function test_it_should_throw_exception_when_requirements_existed_and_visitor_is_guest()
    {
        // prepare
        $auth = Mockery::mock('Illuminate\Auth\AuthManager');
        $auth->shouldReceive('guest')->once()->andReturn(true);

        $log = Mockery::mock('Illuminate\Log\Writer');

        /** @noinspection PhpParamsInspection */
        $guard = new Guard($auth, $log);

        $requirements = [
            'roles'       => [ 'Foo' ],
            'permissions' => [ ],
        ];

        // act
        $guard->censor($requirements);

        // assert

    }

    /**
     * @expectedException \Nam\Guard\Exceptions\UnauthorizedException
     */
    public function test_it_should_throw_exception_when_visitor_is_user_who_did_not_passed_first_required_role()
    {
        // prepare
        $visitor = Mockery::mock('\Nam\Guard\Visitor');
        $visitor->shouldReceive('hasRole')->once()->andReturn(false);

        $auth = Mockery::mock('Illuminate\Auth\AuthManager');
        $auth->shouldReceive('guest')->once()->andReturn(true);

        $log = Mockery::mock('Illuminate\Log\Writer');

        /** @noinspection PhpParamsInspection */
        $guard = new Guard($auth, $log);

        $requirements = [
            'roles'       => [ 'Foo' ],
            'permissions' => [ ],
        ];

        // act
        $guard->censor($requirements);

        // assert
    }
}
