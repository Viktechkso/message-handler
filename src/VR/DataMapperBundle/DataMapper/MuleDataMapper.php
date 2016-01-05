<?php

namespace VR\DataMapperBundle\DataMapper;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use VR\AppBundle\Entity\Datamap;

/**
 * Class MuleDataMapper
 *
 * @package VR\DataMapperBundle\DataMapper
 *
 * @author Jimmie Louis Borch
 */
class MuleDataMapper
{
    /** @var EntityManager */
    private $em;

    function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
        $this->initialize();
    }

    public function loadMapFromFile($fileName)
    {
        $fileName = str_replace('.json', '', $fileName);
        $json = file_get_contents(__DIR__ . '/../Maps/' . $fileName . '.json');

        return new Map(json_decode($json, true));
    }

    public function loadMapFromString($string)
    {
        return new Map(json_decode($string, true));
    }

    public function loadMapFromDatabase($name)
    {
        /** @var Datamap $datamap */
        $datamap = $this->em->getRepository('VRAppBundle:Datamap')->findOneByName($name);
        if (!$datamap) {
            throw new NotFoundHttpException('Map with name ' . $name . ' not found in database.');
        }

        return $this->loadMapFromString($datamap->getMap());
    }

    public function run($map, $payload)
    {
        $dataMapper = new DataMapper();
        $dataMapper->setMap($map);
        $result = $dataMapper->map($payload);

        return $result;
    }

    public function initialize()
    {
        set_error_handler(array($this, 'handleError'));
    }

    public function handleError($severity, $message, $filename, $lineno)
    {
        if (error_reporting() == 0) {
            return;
        }
        if (error_reporting() & $severity) {
            throw new \ErrorException($message, 0, $severity, $filename, $lineno);
        }
    }
}
