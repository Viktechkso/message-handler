<?php

namespace VR\AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProcessScheduleRepository
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 * @author Andrzej Prusinowski <andrzej@avris.it>
 */
class ProcessScheduleRepository extends EntityRepository
{
    public function findForCronTask($cronCounts, $cronId)
    {
        $qb = $this->createQueryBuilder('ps')
            ->where('ps.enabled = true')
            ->andWhere('MOD(ps.id, :cronCounts) = :cronId')
            ->setParameter('cronCounts', $cronCounts)
            ->setParameter('cronId', $cronId);

        return $qb->getQuery()->getResult();
    }
}
