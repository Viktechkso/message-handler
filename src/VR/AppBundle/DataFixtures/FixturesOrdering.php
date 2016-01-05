<?php

namespace VR\AppBundle\DataFixtures;

/**
 * Fixtures ordering helper
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class FixturesOrdering
{
    private static $ordering = array(
        'Message',
        'Error',
        'ProcessSchedule'
    );

    /**
     * Returns ordering for class name
     *
     * @param string $name Class name
     *
     * @return int|string
     */
    public static function getOrdering($name)
    {
        foreach (self::$ordering as $key => $item) {
            if ($item == $name) {
                return $key;
            }
        }
    }
}
