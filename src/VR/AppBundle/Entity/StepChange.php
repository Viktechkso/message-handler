<?php

namespace VR\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Step change
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 *
 * @ORM\Entity(repositoryClass="VR\AppBundle\Entity\Repository\StepChangeRepository")
 * @ORM\Table(name="step_changes")
 * @ORM\HasLifecycleCallbacks
 */
class StepChange
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
     * @var \DateTime
	 * @ORM\Column(name="changed_timestamp", type="datetime")
	 */
	protected $changedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="stepChanges")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $message;

    /**
     * @ORM\Column(name="message_status_before", type="string", length=25)
     */
    protected $messageStatusBefore;

    /**
     * @ORM\Column(name="message_status_after", type="string", length=25)
     */
    protected $messageStatusAfter;

    /**
     * @ORM\Column(name="step_number", type="integer", nullable=true)
     */
    protected $stepNumber;

    public function __construct()
    {
        $this->changedAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getChangedAt()
    {
        return $this->changedAt;
    }

    /**
     * @param mixed $changedAt
     * @return $this
     */
    public function setChangedAt($changedAt)
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessageStatusAfter()
    {
        return $this->messageStatusAfter;
    }

    /**
     * @param mixed $messageStatusAfter
     * @return $this
     */
    public function setMessageStatusAfter($messageStatusAfter)
    {
        $this->messageStatusAfter = $messageStatusAfter;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessageStatusBefore()
    {
        return $this->messageStatusBefore;
    }

    /**
     * @param mixed $messageStatusBefore
     * @return $this
     */
    public function setMessageStatusBefore($messageStatusBefore)
    {
        $this->messageStatusBefore = $messageStatusBefore;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStepNumber()
    {
        return $this->stepNumber;
    }

    /**
     * @param mixed $stepNumber
     * @return $this
     */
    public function setStepNumber($stepNumber)
    {
        $this->stepNumber = $stepNumber;

        return $this;
    }
}
