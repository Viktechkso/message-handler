<?php

namespace VR\DataMapperBundle\DataMapper;

/**
 * Class MapElementFactory
 *
 * @package VR\DataMapperBundle\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class MapElementFactory
{
    /**
     * @param array $array
     * @return MapElement
     */
    public static function createFromArray(array $array)
    {
        Object::keyExists(MapElement::KEY_SOURCE, $array, $source);
        if (!Object::keyExists(MapElement::KEY_TARGET, $array, $target)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Target key\'%s\' must be present in the map element, received object \'%s\'.',
                    MapElement::KEY_TARGET,
                    json_encode($array)
                )
            );
        }
        $element = new MapElement($source, $target);
        foreach (array(MapElement::KEY_FORMAT, MapElement::KEY_MAPPING, MapElement::KEY_DEFAULT, MapElement::KEY_MODE) as $key) {
            if (Object::keyExists($key, $array)) {
                $method = 'set' . ucfirst($key);
                $element->$method($array[$key]);
            }
        }

        if (isset($array[MapElement::DATE_CHECK_KEY])) {
            $element->setDateCheck($array[MapElement::DATE_CHECK_KEY]);
        }

        return $element;
    }
}
