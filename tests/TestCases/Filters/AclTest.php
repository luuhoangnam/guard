<?php


namespace Nam\Guard\TestCases\Filters;

use Mockery;
use Mockery\Expectation;
use Mockery\MockInterface;
use Nam\Guard\Filters\Acl;


/**
 * RolesTest Test Case
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard\TestCases\Filters
 *
 */
class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Expectation
     */
    private $censorMethod;

    protected function tearDown()
    {
        Mockery::close();
    }

    public function test_it_does_not_filter_closure_action()
    {
        $config = [
            'route' => [
                'getActionName' => [ 1, 'Closure' ],
                'getName'       => 'foo',
            ],
            'log'   => true,
        ];

        $app = $this->mockApp($config);
        $guard = $this->mockGuard($config);
        $route = $this->mockRoute($config);

        $aclFilter = new Acl($app, $guard);

        // act
        $result = $aclFilter->filter($route);

        // assert
        $this->assertNull($result);
    }

    /**
     * Almost is test extract requirements trait
     */
    public function test_filter()
    {
        // prepare
        $config = [
            'requirements' => [
                'roles'       => [ 'Foo', ],
                'permissions' => [ ],
            ],
            'route'        => [
                'getActionName' => [ 2, '\Nam\Guard\TestCases\Stubs\Controllers\AclController@store' ],
            ]
        ];

        $app = $this->mockApp($config);
        $guard = $this->mockGuard($config);
        $route = $this->mockRoute($config);

        $aclFilter = new Acl($app, $guard);

        // act
        $result = $aclFilter->filter($route);

        // assert
        $this->censorMethod->verify();
        $this->assertEquals('ok', $result);
    }

    public function test_can_handle_multiple_roles()
    {
        // prepare
        $config = [
            'requirements' => [
                'roles'       => [ 'Foo', 'Bar' ],
                'permissions' => [ ],
            ],
            'route'        => [
                'getActionName' => [ 2, '\Nam\Guard\TestCases\Stubs\Controllers\AclMultipleRolesController@store' ],
            ]
        ];

        $app = $this->mockApp($config);
        $guard = $this->mockGuard($config);
        $route = $this->mockRoute($config);

        $aclFilter = new Acl($app, $guard);

        // act
        $result = $aclFilter->filter($route);

        // assert
        $this->censorMethod->verify();
        $this->assertEquals('ok', $result);
    }

    public function test_can_handle_multiple_permissions()
    {
        // prepare
        $config = [
            'requirements' => [
                'permissions' => [ 'foo', 'baz' ],
                'roles'       => [ ],
            ],
            'route'        => [
                'getActionName' => [ 2, '\Nam\Guard\TestCases\Stubs\Controllers\AclMultiplePermissionsController@store' ],
            ]
        ];

        $app = $this->mockApp($config);
        $guard = $this->mockGuard($config);
        $route = $this->mockRoute($config);

        $aclFilter = new Acl($app, $guard);

        // act
        $result = $aclFilter->filter($route);

        // assert
        $this->censorMethod->verify();
        $this->assertEquals('ok', $result);
    }

    /**
     * @param array $config
     *
     * @return \Illuminate\Foundation\Application|MockInterface
     */
    protected function mockApp(array $config)
    {
        $log = $this->mockLog($config);

        /** @var MockInterface|\Illuminate\Foundation\Application $app */
        $app = Mockery::mock('Illuminate\Foundation\Application');
        $app->shouldReceive('make')->once()->with('log')->andReturn($log);

        return $app;
    }

    /**
     * @param array $config
     *
     * @return \Illuminate\Log\Writer|MockInterface
     */
    protected function mockLog(array $config)
    {
        $log = Mockery::mock('Illuminate\Log\Writer');

        if (isset( $config['log'] )) {
            $log->shouldReceive('warning')->once()->andReturn($config['log']);
        }

        return $log;
    }

    /**
     * @param array $config
     *
     * @return MockInterface|\Nam\Guard\Guard
     */
    protected function mockGuard(array $config)
    {
        $guard = Mockery::mock('Nam\Guard\Guard');

        if (isset( $config['requirements'] )) {
            $this->censorMethod = $guard->shouldReceive('censor')->once()->with($config['requirements'])->andReturn('ok');
        }

        return $guard;
    }

    /**
     * @param array $config
     *
     * @return \Illuminate\Routing\Route|MockInterface
     */
    protected function mockRoute(array $config)
    {
        /** @var MockInterface|\Illuminate\Routing\Route $route */
        $route = Mockery::mock('Illuminate\Routing\Route');

        if (isset( $config['route']['getActionName'] )) {
            for ($i = 0; $i < $config['route']['getActionName'][0]; $i ++) {
                $route->shouldReceive('getActionName')
                      ->once()
                      ->andReturn($config['route']['getActionName'][1]);
            }
        }

        if (isset( $config['route']['getName'] )) {
            $route->shouldReceive('getName')->once()->andReturn($config['route']['getName']);
        }

        return $route;
    }
}
