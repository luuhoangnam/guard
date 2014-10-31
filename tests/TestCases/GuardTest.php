<?php


namespace Nam\Guard\TestCases;

use Mockery;
use Mockery\MockInterface;
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
        $config = [
            'guest' => true,
        ];

        $auth = $this->mockAuth($config);

        $log = $this->mockLog();

        /** @noinspection PhpParamsInspection */
        $guard = new Guard($auth, $log);

        $requirements = [
            'roles'       => [ 'Foo', 'Bar' ],
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
        $config = [
            'guest'   => false,
            'hasRole' => [
                'Foo' => false,
            ],
        ];

        $auth = $this->mockAuth($config);

        $log = $this->mockLog();

        /** @noinspection PhpParamsInspection */
        $guard = new Guard($auth, $log);

        $requirements = [
            'roles'       => [ 'Foo', 'Bar' ],
            'permissions' => [ ],
        ];

        // act
        $guard->censor($requirements);

        // assert
    }

    /**
     * @expectedException \Nam\Guard\Exceptions\UnauthorizedException
     */
    public function test_it_should_throw_exception_when_visitor_is_user_who_did_not_passed_second_required_role()
    {
        // prepare
        $config = [
            'guest'   => false,
            'hasRole' => [
                'Foo' => true,
                'Bar' => false,
            ],
        ];

        $auth = $this->mockAuth($config);

        $log = $this->mockLog();

        /** @noinspection PhpParamsInspection */
        $guard = new Guard($auth, $log);

        $requirements = [
            'roles'       => [ 'Foo', 'Bar' ],
            'permissions' => [ ],
        ];

        // act
        $guard->censor($requirements);

        // assert
    }

    /**
     * @param $config
     *
     * @return MockInterface
     */
    protected function mockAuth($config)
    {
        $auth = Mockery::mock('Illuminate\Auth\AuthManager');

        $auth->shouldReceive('guest')->once()->andReturn($config['guest']);

        if ( ! $config['guest']) {
            $visitor = $this->mockVisitor($config);
            $auth->shouldReceive('user')->once()->andReturn($visitor);
        }

        return $auth;
    }

    /**
     * @param array $config
     *
     * @return MockInterface
     */
    protected function mockVisitor(array $config)
    {
        $visitor = Mockery::mock('\Nam\Guard\Visitor');

        if (isset( $config['hasRole'] )) {
            $this->hasRole($visitor, $config['hasRole']);
        }

        return $visitor;
    }

    /**
     * @param MockInterface $visitor
     * @param array         $roles
     */
    protected function hasRole(MockInterface $visitor, array $roles)
    {
        foreach ($roles as $role => $return) {
            $visitor->shouldReceive('hasRole')->once()->with($role)->andReturn($return);
        }
    }

    /**
     * @return MockInterface
     */
    protected function mockLog()
    {
        $log = Mockery::mock('Illuminate\Log\Writer');

        return $log;
    }

}
