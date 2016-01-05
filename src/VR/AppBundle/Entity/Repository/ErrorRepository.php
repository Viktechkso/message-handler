<?php

namespace VR\AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use VR\AppBundle\Entity\Message;

/**
 * ErrorRepository
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ErrorRepository extends EntityRepository
{
    public function getLastStepNumberWithError(Message $message)
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.stepNo')
            ->where('e.message = :message')
            ->orderBy('e.stepNo', 'desc')
            ->setMaxResults(1);

        $qb->setParameter('message', $message);

        $results = $qb->getQuery()->getResult();

        if (count($results)) {
            return $results[0]['stepNo'];
        }

        return false;
    }

    /**
     * Counts Message's errors created in the last $hours hours.
     *
     * @param $message
     * @param $hours
     *
     * @return mixed
     */
    public function countErrorsInTimeForMessage(Message $message, $hours)
    {
        $stepNumber = $this->getLastStepNumberWithError($message);

        if (!$stepNumber) {
            return 0;
        }

        $date = new \DateTime('-' . $hours . ' hours');

        $qb = $this->createQueryBuilder('e')
            ->select('count(e.id)')
            ->where('e.message = :message')
            ->andWhere('e.entryAt >= :date')
            ->andWhere('e.stepNo = :stepNumber');

        $qb->setParameter('message', $message);
        $qb->setParameter('date', $date);
        $qb->setParameter('stepNumber', $stepNumber);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
