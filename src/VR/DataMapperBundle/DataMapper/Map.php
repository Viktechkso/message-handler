<?php

namespace VR\DataMapperBundle\DataMapper;

/**
 * Class Map
 *
 * @package VR\DataMapperBundle\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class Map
{
    /**
     * @var array
     */
    private $mapping;

    public function __construct($mapping = array())
    {
        $this->setMapping($mapping);
    }

    public function setMapping(array $mapping)
    {
        foreach ($mapping as &$mapElement) {
            if ($mapElement instanceof MapElement) {
                continue;
            } elseif (is_array($mapElement)) {
                $mapElement = MapElementFactory::createFromArray($mapElement);
            } else {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Each map element must be of MapElementInterface or an array, %s given',
                        gettype($mapElement)
                    )
                );
            }
        }
        $this->mapping = $mapping;
    }

    /**
     * @return MapElement[]
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param array $input
     * @return mixed
     */
    public function map($input)
    {
        $output = null;
        foreach ($this->getMapping() as $mapElement) {
            $mapElement->map($input, $output);
        }
        return $output;
    }
}
