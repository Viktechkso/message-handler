<?php

namespace VR\AppBundle\Entity;

use VR\AppBundle\Plugin\PluginManager;
use VR\AppBundle\Entity\ProcessSchedule;
use VR\AppBundle\Service\System;

/**
 * Class ProcessQueue
 *
 * @package VR\AppBundle\Entity
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ProcessQueue
{
    /** @var ProcessSchedule[]  */
    private $queue = array();

    private $dir;

    private $pid;

    /**
     * @param ProcessSchedule[] $processes
     */
    public function __construct($processes, $now, $dir)
    {
        foreach ($processes as $process) {
            if ($process->canBeRunAt($now)) {
                $this->queue[] = $process;
            }
        }
        $this->dir = $dir;
        $this->pid = getmypid();
    }

    public function count()
    {
        return count($this->queue);
    }

    public function dequeue()
    {
        $current = array_shift($this->queue);
        if (!$current) { return null; }

        $semFile = $this->dir . $current->getType();

        if ($this->isLocked($semFile)) {
            if (in_array($current->getType(), ProcessSchedule::$sqlTypes)) {
                $this->queue[] = $current;
                sleep(5);
            }
            return $this->dequeue();
        }

        file_put_contents($semFile, getmypid());

        return $current;
    }

    private function isLocked($semFile)
    {
        return file_exists($semFile) && ($this->isFileYoungerThanHour($semFile) || $this->isProcessRunning($semFile));
    }

    private function isFileYoungerThanHour($semFile)
    {
        return filemtime($semFile) > time() - 60 * 60;
    }

    private function isProcessRunning($semFile)
    {
        $pid = (int)trim(file_get_contents($semFile));

        $systemHelper = new System();

        return $systemHelper->isProcessRunningByPid($pid);
    }

    public function unlock($type)
    {
        @unlink($this->dir . $type);
    }

}
