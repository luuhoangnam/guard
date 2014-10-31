<?php


namespace Nam\Guard;


/**
 * Interface Visitor
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard
 *
 */
interface Visitor
{
    /**
     * Checks if the user has a Role by its name.
     *
     * @param string $name Role name.
     *
     * @return bool
     */
    public function hasRole($name);

    /**
     * Check if user has a permission by its name.
     *
     * @param string $permission Permission string.
     *
     * @return bool
     */
    public function can($permission);
}