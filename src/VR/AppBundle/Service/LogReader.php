<?php

namespace VR\AppBundle\Service;

use VR\AppBundle\Form\LogsFilterData;

/**
 * Class LogReader
 *
 * @package VR\AppBundle\Service
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 */
class LogReader
{
    private $dir;

    public function __construct($logdir)
    {
        $this->dir = $logdir;
    }

    public function getFileList()
    {
        $files = [];
        foreach (glob($this->dir . '/*.log') as $file) {
            $parts = explode('/', $file);
            $filename = end($parts);
            $files[$filename] = $filename;
        }

        return $files;
    }

    public function read(LogsFilterData $filter)
    {
        $entries = file($this->dir . '/' . $filter->filename);

        $searchedDate = $filter->date->format('Y-m-d');

        $out = [];
        foreach ($entries as $entry) {
            if (preg_match('#^\[((\d{4}-\d{2}-\d{2}) \d{2}:\d{2}:\d{2})\] (.*): (.*)$#Ui', $entry, $matches)) {
                list($line, $datetime, $date, $type, $message) = $matches;

                if ($date !== $searchedDate) {
                    continue;
                }
                if ($filter->type && $type !== $filter->type) {
                    continue;
                }
                if ($filter->search && stripos($message, $filter->search) === false) {
                    continue;
                }

                $out[] = [
                    'datetime' => $datetime,
                    'type' => $type,
                    'message' => $message,
                ];
            }
        }

        return $out;
    }
}
