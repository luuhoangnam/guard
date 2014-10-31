<?php


namespace Nam\Guard\TestCases\Stubs\Controllers;

use Illuminate\Routing\Controller;


/**
 * Class AclController
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard\TestCases\Stubs\Controllers
 *
 */
class AclMultipleRolesController extends Controller
{
    /**
     * @roles Foo|Bar
     */
    public function store()
    {
        // No-op
    }
}