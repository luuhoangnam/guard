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
class AclController extends Controller
{
    /**
     * @roles Foo
     */
    public function store()
    {
        // No-op
    }
}