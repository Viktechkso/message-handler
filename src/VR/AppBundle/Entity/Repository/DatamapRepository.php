<?php

namespace VR\AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * DatamapRepository
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class DatamapRepository extends EntityRepository
{
    public function findAllForJsonList()
    {
        $datamaps = $this->findAll();

        $results = [];

        if (count($datamaps)) {
            foreach ($datamaps as $datamap) {
                $results[] = [
                    'name' => $datamap->getName(),
                    'type' => $datamap->getType(),
                    'map' => base64_encode($datamap->getMap()),
                    'description' => $datamap->getDescription()
                ];
            }
        }

        return $results;
    }
}
