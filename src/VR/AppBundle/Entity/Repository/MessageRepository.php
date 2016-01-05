<?php

namespace VR\AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use VR\AppBundle\Entity\Message;
use VR\AppBundle\Form\MessageSearchData;

/**
 * MessageRepository
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class MessageRepository extends EntityRepository
{
    public function searchQB(MessageSearchData $parameters)
    {
        $qb = $this->createQueryBuilder('m');

        if ($parameters->flowName) {
            $qb->andWhere('m.flowName LIKE :flowName');
            $qb->setParameter('flowName', '%' . $parameters->flowName . '%');
        }

        if ($parameters->flowStatuses) {
            $qb->andWhere('m.flowStatus IN (:flowStatuses)');
            $qb->setParameter('flowStatuses', $parameters->flowStatuses);
        }

        if ($parameters->createdAtFrom) {
            $qb->andWhere('m.flowCreatedAt >= :createdAtFrom');
            $qb->setParameter('createdAtFrom', $parameters->createdAtFrom);
        }

        if ($parameters->createdAtTo) {
            $parameters->createdAtTo->setTime(
                $parameters->createdAtTo->format('H'),
                $parameters->createdAtTo->format('i'),
                60
            );
            $qb->andWhere('m.flowCreatedAt <= :createdAtTo');
            $qb->setParameter('createdAtTo', $parameters->createdAtTo);
        }

        if ($parameters->containing) {
            $qb->andWhere('(lower(m.flowMessage) LIKE :payload OR lower(m.flow) LIKE :steps)');
            $qb->setParameter('payload', '%' . $parameters->containing . '%');
            $qb->setParameter('steps', '%' . $parameters->containing . '%');
        }

        return $qb;
    }

    public function countStatuses()
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.flowStatus as name, count(m.id) as counter')
            ->groupBy('m.flowStatus');

        return $qb->getQuery()->getScalarResult();
    }

    public function getStatusCounters()
    {
        $counters = $this->countStatuses();
        $results = [];

        foreach ($counters as $counter) {
            $results[$counter['name']] = $counter['counter'];
        }

        return $results;
    }

    public function getStatusNames($excludes = [])
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.flowStatus as name')
            ->groupBy('m.flowStatus');

        $results = $qb->getQuery()->getScalarResult();
        $names = [];

        if (count($results)) {
            foreach ($results as $result) {
                if (!in_array($result['name'], $excludes)) {
                    $names[] = $result['name'];
                }
            }
        }

        return $names;
    }

    public function getStatusesForSearch()
    {
        $alwaysVisibleStatuses = [
            Message::STATUS_NEW,
            Message::STATUS_IN_PROGRESS,
            Message::STATUS_ERROR,
            Message::STATUS_HALTED,
            Message::STATUS_FINISHED,
            Message::STATUS_CANCELLED
        ];

        $statusesInDatabase = $this->getStatusNames();

        $allStatuses = $alwaysVisibleStatuses + $statusesInDatabase;

        $allStatuses = array_combine($allStatuses, $allStatuses); // copy values to keys

        return $allStatuses;
    }

    public function getUnfinishedByType($type, $limit = null)
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.flowName = :flowName')
            ->andWhere('m.flowStatus = :statusNew')
            ->andWhere('m.forced = false')
            ->setParameter('flowName', $type)
            ->setParameter('statusNew', Message::STATUS_NEW);

        $this->addRunAtQuery($qb);

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function getOneUnfinishedForced()
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.flowStatus = :statusNew')
            ->andWhere('m.forced = true')
            ->setParameter('statusNew', Message::STATUS_NEW)
            ->setMaxResults(1);

        $this->addRunAtQuery($qb);

        $results = $qb->getQuery()->getResult();

        return reset($results);
    }

    protected function addRunAtQuery(QueryBuilder $qb)
    {
        $qb
            ->addOrderBy('m.runAt', 'asc')
            ->addOrderBy('m.id', 'asc')
            ->andWhere('(m.runAt <= :now OR m.runAt IS NULL)')
            ->setParameter('now', new \DateTime());

    }

    public function insert($type, $status, $steps, $payload, $ignore = true, $forcedMessages = false)
    {
        if ($ignore) {
            $sql = <<<SQL
INSERT IGNORE INTO messages (message_type, message_status, message_steps, message_payload, message_created, unique_md5, forced)
VALUES (:type, :status, :steps, :payload, :createdAt, :md5, :forced);
SQL;
            $hashSuffix = null;
        } else {
            $sql = <<<SQL
INSERT INTO messages (message_type, message_status, message_steps, message_payload, message_created, unique_md5, forced)
VALUES (:type, :status, :steps, :payload, :createdAt, :md5, :forced);
SQL;
            $hashSuffix = uniqid(); # md5 must be unique, but we want to force insert new message
        }

        $now = new \DateTime();

        $params = [
            'type' => $type,
            'status' => $status,
            'steps' => $steps,
            'payload' => $payload,
            'createdAt' => $now->format('Y-m-d H:i:s'),
            'md5' => md5($steps . $payload . $type . $hashSuffix),
            'forced' => $forcedMessages
        ];

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute($params);

        $id = $this->getEntityManager()->getConnection()->lastInsertId();

        return $id == 0 ? false : $id;
    }
}
