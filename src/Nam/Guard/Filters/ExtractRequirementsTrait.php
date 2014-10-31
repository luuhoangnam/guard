<?php


namespace Nam\Guard\Filters;

use InvalidArgumentException;


/**
 * Trait ExtractRequirementsTrait
 *
 * @author  Nam Hoang Luu <nam@mbearvn.com>
 * @package Nam\Guard\Filters
 *
 */
trait ExtractRequirementsTrait
{
    /**
     * @param string $parameters
     * @param array  $allowedSeparators
     *
     * @return array
     */
    protected function extractRequirements($parameters, array $allowedSeparators = [ '+' ])
    {
        if (empty( $allowedSeparators )) {
            $message = "Allowed separators must be an array.";

            throw new InvalidArgumentException($message);
        }

        $requirements = [
            'roles'       => [ ],
            'permissions' => [ ],
        ];

        $permissions = [ ];
        if (in_array('+', $allowedSeparators)) {
            $permissions = explode('+', $parameters);
        }

        foreach ($permissions as $permission) {
            $requirements['permissions'][] = trim($permission);
        }

        return $requirements;
    }
}