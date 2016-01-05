<?php

namespace VR\AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SymfonyTestCase
 *
 * @package VR\AppBundle\Tests
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class SymfonyTestCase extends WebTestCase
{
    public function checkGetterAndSetter($object, $fieldName, $fieldValue, $returnThisFromSetter)
    {
        $setterName = 'set' . ucfirst($fieldName);
        $getterName = 'get' . ucfirst($fieldName);

        $setterReturnValue = $object->$setterName($fieldValue);

        if ($returnThisFromSetter) {
            $class = get_class($object);
            $this->assertTrue($setterReturnValue instanceof $class);
        }

        $getterReturnValue = $object->$getterName();

        $this->assertEquals($fieldValue, $getterReturnValue);
    }

    public function checkGetId($object)
    {
        $this->assertNull($object->getId());

        $refObject   = new \ReflectionObject($object);
        $refProperty = $refObject->getProperty('id');
        $refProperty->setAccessible(true);
        $refProperty->setValue($object, 12345);

        $this->assertEquals(12345, $object->getId());
    }
}