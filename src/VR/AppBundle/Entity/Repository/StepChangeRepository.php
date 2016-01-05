<?php

namespace VR\AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use VR\AppBundle\Entity\Message;

/**
 * StepChangeRepository
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class StepChangeRepository extends EntityRepository
{
    public function findOrderedByMessage(Message $message)
    {
        $qb = $this->createQueryBuilder('sc')
            ->where('sc.message = :message')
            ->setParameter('message', $message)
            ->orderBy('sc.changedAt', 'asc');

        return $qb->getQuery()->getResult();
    }
}
