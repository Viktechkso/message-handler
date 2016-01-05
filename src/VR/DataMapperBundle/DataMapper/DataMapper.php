<?php

namespace VR\DataMapperBundle\DataMapper;

/**
 * Class DataMapper
 *
 * @package VR\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class DataMapper
{
    /**
     * @var Map
     */
    private $map;

    public function setMap(Map $map)
    {
        $this->map = $map;
    }

    public function getMap()
    {
        return $this->map;
    }

    public function map($input)
    {
        $output = null;
        foreach ($this->map->getMapping() as $mapElement) {
            $mapElement->map($input, $output);
        }
        return $output;
    }
}
