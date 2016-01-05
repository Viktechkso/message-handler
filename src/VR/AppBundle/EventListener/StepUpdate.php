<?php

namespace VR\AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VR\AppBundle\Entity\Message;
use VR\AppBundle\Entity\StepChange;

/**
 * Class StepUpdate
 *
 * @package VR\AppBundle\EventListener
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class StepUpdate implements EventSubscriber
{
    protected $oldStatus;

    public function getSubscribedEvents()
    {
        return array(
            'preUpdate',
            'postUpdate',
        );
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Message) {
            $this->oldStatus = $this->getMessageOldStatus($entityManager, $entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Message) {
            $this->createStepChangeEntity($entityManager, $entity, $this->oldStatus);
        }
    }

    protected function getMessageOldStatus($entityManager, $message)
    {
        $sqlResults = $entityManager
            ->getConnection()
            ->executeQuery('select message_status from messages where id = ' . $message->getId())
            ->fetch();

        return $sqlResults['message_status'];
    }

    protected function createStepChangeEntity($entityManager, $message, $oldStatus)
    {
        $currentStepNumber = $message->getCurrentStepNumber();

        if ($currentStepNumber) {
            $sql = '
                insert into step_changes (changed_timestamp, message_id, message_status_before, message_status_after, step_number)
                values (NOW(), "' . $message->getId() . '", "' . $oldStatus . '", "' . $message->getFlowStatus() . '", "' . $message->getCurrentStepNumber() . '")
                ';
        } else {
            // to avoid putting "0" in step number
            $sql = '
                insert into step_changes (changed_timestamp, message_id, message_status_before, message_status_after)
                values (NOW(), "' . $message->getId() . '", "' . $oldStatus . '", "' . $message->getFlowStatus() . '")
                ';
        }

        $entityManager
            ->getConnection()
            ->executeQuery($sql);
    }
}