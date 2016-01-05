<?php

namespace VR\AppBundle\Service;

/**
 * Class CronHelper
 *
 * @package VR\AppBundle\Service
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 */
class CronHelper
{
    public static function getCronPossibilities($schema)
    {
        $all = range(0, 59);

        if ($schema === null) {
            return $all;
        }

        list($base, $divide) = explode('/', $schema . '/');

        if (preg_match('/^[0-9]+$/', $base)) {
            $all = [$base];
        } else {
            list($from, $to) = explode('-', $base . '-');

            if ($to) {
                $all = range($from, $to);
            }

            $list = explode(',', $base);
            if (count($list) > 1) {
                $all = $list;
            }
        }

        if ($divide) {
            $all = array_filter($all, function ($nr) use ($divide) {
                return $nr % $divide == 0;
            });
        }

        return $all;
    }
}
