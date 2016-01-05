<?php

namespace VR\AppBundle\Service;

/**
 * Class System
 *
 * @package VR\AppBundle\Service
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class System
{
    public function isProcessRunningByPid($pid)
    {
        return file_exists('/proc/' . $pid);
    }
}
