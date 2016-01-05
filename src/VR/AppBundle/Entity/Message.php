<?php

namespace VR\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 * @author Jimmie Louis Borch
 *
 * @ORM\Entity(repositoryClass="VR\AppBundle\Entity\Repository\MessageRepository")
 * @ORM\Table(name="messages", indexes={
 *     @ORM\Index(name="md5_index", columns={"unique_md5"}),
 *     @ORM\Index(name="message_type", columns={"message_type"}),
 *     @ORM\Index(name="message_status", columns={"message_status"}),
 *     @ORM\Index(name="forced", columns={"forced"}),
 *     @ORM\Index(name="run_at", columns={"run_at"}),
 * })
 * @ORM\HasLifecycleCallbacks
 */
class Message
{
    const STATUS_NEW = 'New';
    const STATUS_IN_PROGRESS = 'In progress';
    const STATUS_RERUN = 'Rerun';
    const STATUS_ERROR = 'Error';
    const STATUS_FINISHED = 'Finished';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_HALTED = 'Halted';

    public static $allowedStatuses = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_RERUN,
        self::STATUS_ERROR,
        self::STATUS_FINISHED,
        self::STATUS_CANCELLED,
        self::STATUS_HALTED
    ];

    public static $completedStatuses = [
        'Done',
        'Completed',
        'Finished',
    ];

	/**
	 * @ORM\Id
	 * @ORM\Column(type="bigint")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(name="message_created", type="datetime")
	 */
	protected $flowCreatedAt;

    /**
     * Contains JSON with information about all steps.
     *
     * @ORM\Column(name="message_steps", type="text")
     */
    protected $flow;

    public $prettyFlowLastError;

    /**
     * @ORM\Column(name="message_type", type="string", length=255)
     */
    protected $flowName;

    /**
     * @ORM\Column(name="message_status", type="string", length=25)
     */
    protected $flowStatus;

    /**
     * @ORM\Column(name="message_payload", type="text")
     */
    protected $flowMessage;

    public $prettyFlowMessageLastError;

    /**
     * @ORM\OneToMany(targetEntity="Error", mappedBy="message")
     * @ORM\OrderBy({"entryAt" = "DESC"})
     */
    protected $errors;

    /**
     * @ORM\OneToMany(targetEntity="StepChange", mappedBy="message")
     */
    protected $stepChanges;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $forced;

    /**
     * @ORM\Column(name="run_at", type="datetime", nullable=true)
     */
    protected $runAt;

    /**
     * @ORM\Column(name="unique_md5", unique=true)
     */
    protected $md5;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->forced = false;
        $this->setFlowCreatedAt(new \DateTime());

        $this->errors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->stepChanges = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function generateMd5()
    {
        if (!$this->md5) {
            $this->md5 = md5($this->flow . $this->flowMessage . $this->flowName);
        }
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set flowCreatedAt
     *
     * @param \DateTime $flowCreatedAt
     * @return Message
     */
    public function setFlowCreatedAt($flowCreatedAt)
    {
        $this->flowCreatedAt = $flowCreatedAt;

        return $this;
    }

    /**
     * Get flowCreatedAt
     *
     * @return \DateTime 
     */
    public function getFlowCreatedAt()
    {
        return $this->flowCreatedAt;
    }

    /**
     * Set flow
     *
     * @param string $steps
     * @return Message
     */
    public function setFlow($steps)
    {
        $this->flow = $steps;

        return $this;
    }

    /**
     * Get flow
     *
     * @return string 
     */
    public function getFlow()
    {
        return $this->flow;
    }

    public function getStepsArray()
    {
        $parsedJson = json_decode($this->flow, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Steps JSON parsing error: ' . json_last_error_msg());
        }

        return $parsedJson;
    }

    public function getPrettyFlow()
    {
        $json = json_decode($this->flow, true);

        $this->prettyFlowLastError = json_last_error_msg();

        if (!$json) return null;

        return json_encode($json, JSON_PRETTY_PRINT);
    }

    /**
     * Set flowStatus
     *
     * @param integer $flowStatus
     * @return Message
     */
    public function setFlowStatus($flowStatus)
    {
        $this->flowStatus = $flowStatus;

        return $this;
    }

    /**
     * Get flowStatus
     *
     * @return integer 
     */
    public function getFlowStatus()
    {
        return $this->flowStatus;
    }

    public function isStatus($statusName)
    {
        return strtolower($this->flowStatus) == strtolower($statusName);
    }

    public function getFlowStatusCssClass()
    {
        $cssClasses = [
            'Error' => 'label-danger',
            'In progress' => 'label-primary',
            'Halted' => 'label-warning'
        ];

        return isset($cssClasses[$this->flowStatus]) ? $cssClasses[$this->flowStatus] : 'label-default';
    }

    /**
     * Set flowMessage
     *
     * @param string $payload
     * @return Message
     */
    public function setFlowMessage($payload)
    {
        $this->flowMessage = $payload;

        return $this;
    }

    /**
     * Get flowMessage
     *
     * @return string 
     */
    public function getFlowMessage()
    {
        return $this->flowMessage;
    }

    public function getPayloadArray()
    {
        return json_decode($this->flowMessage, true);
    }

    public function setPayloadArray($array)
    {
        $this->flowMessage = json_encode($array);
    }

    public function getPrettyFlowMessage()
    {
        $json = json_decode($this->flowMessage, true);

        $this->prettyFlowMessageLastError = json_last_error_msg();

        if (!$json) return null;

        $json = json_encode($json, JSON_PRETTY_PRINT);

        return $json;
    }

    /**
     * Add errors
     *
     * @param \VR\AppBundle\Entity\Error $errors
     * @return Message
     */
    public function addError(\VR\AppBundle\Entity\Error $errors)
    {
        $this->errors[] = $errors;

        return $this;
    }

    /**
     * Remove errors
     *
     * @param \VR\AppBundle\Entity\Error $errors
     */
    public function removeError(\VR\AppBundle\Entity\Error $errors)
    {
        $this->errors->removeElement($errors);
    }

    /**
     * Get errors
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorsForStep($stepNumber)
    {
        $errors = new ArrayCollection();

        if (count($this->errors)) {
            foreach ($this->errors as $error) {
                if ($error->getStepNo() == $stepNumber) {
                    $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    /**
     * @return mixed
     */
    public function getFlowName()
    {
        return $this->flowName;
    }

    /**
     * @param mixed $messageType
     */
    public function setFlowName($messageType)
    {
        $this->flowName = $messageType;
    }

    /**
     * @return mixed
     */
    public function getStepChanges()
    {
        return $this->stepChanges;
    }

    public function changeStepStatus($stepNumber, $newStatus)
    {
        $this->changeStepParameter($stepNumber, 'Status', $newStatus);

        return $this;
    }

    public function changeStepParameter($stepNumber, $parameter, $value)
    {
        $steps = json_decode($this->flow, true);

        if (isset($steps[$stepNumber])) {
            $steps[$stepNumber][$parameter] = $value;
        } else {
            throw new \Exception('Step with number ' . $stepNumber . ' not found.');
        }

        $this->flow = json_encode($steps);

        return $this;
    }

    public function batchChangeStepStatuses($from, $to)
    {
        $steps = json_decode($this->flow, true);

        if (count($steps)) {
            foreach ($steps as $stepNumber => $stepData) {
                if (strtolower($stepData['Status']) == strtolower($from) || $from === null) {
                    $steps[$stepNumber]['Status'] = $to;
                }
            }
        }

        $this->flow = json_encode($steps);

        return $this;
    }

    public function resetGuids()
    {
        $steps = json_decode($this->flow, true);

        if (count($steps)) {
            foreach ($steps as $stepNumber => $stepData) {
                $steps[$stepNumber]['GUID'] = '';
            }
        }

        $this->flow = json_encode($steps);
    }

    public function isActionAllowed($actionName)
    {
        switch ($actionName) {
            case 'run':
                return !in_array(strtolower($this->getFlowStatus()), ['in progress', 'finished', 'cancelled']);
            case 'new':
                return !in_array(strtolower($this->getFlowStatus()), ['finished', 'new']);
            case 'halt':
                return !in_array(strtolower($this->getFlowStatus()), ['finished', 'halted', 'cancelled']);
            case 'cancel':
                return !in_array(strtolower($this->getFlowStatus()), ['finished', 'cancelled']);
            case 'reset_guids':
                return !in_array(strtolower($this->getFlowStatus()), ['finished']);
            default:
                throw new \InvalidArgumentException('This action name is not allowed.');
        }
    }

    /**
     * Add stepChanges
     *
     * @param \VR\AppBundle\Entity\StepChange $stepChanges
     * @return Message
     */
    public function addStepChange(\VR\AppBundle\Entity\StepChange $stepChanges)
    {
        $this->stepChanges[] = $stepChanges;

        return $this;
    }

    /**
     * Remove stepChanges
     *
     * @param \VR\AppBundle\Entity\StepChange $stepChanges
     */
    public function removeStepChange(\VR\AppBundle\Entity\StepChange $stepChanges)
    {
        $this->stepChanges->removeElement($stepChanges);
    }

    /**
     * @return mixed
     */
    public function getForced()
    {
        return $this->forced;
    }

    /**
     * @param mixed $forced
     */
    public function setForced($forced)
    {
        $this->forced = $forced;
    }

    /**
     * @return mixed
     */
    public function getRunAt()
    {
        return $this->runAt;
    }

    /**
     * @param mixed $runAt
     */
    public function setRunAt($runAt)
    {
        $this->runAt = $runAt;
    }

    public function getCurrentStepNumber()
    {
        $steps = $this->getStepsArray();

        if ($steps && count($steps)) {
            foreach ($steps as $stepNumber => $stepData) {
                if ($stepData['GUID'] == null) {
                    return $stepNumber;
                }
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * @param mixed $md5
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
    }

    public function isFinished()
    {
        $steps = $this->getStepsArray();

        $isFinished = true;

        if (count($steps)) {
            foreach ($steps as $step) {
                if (strtolower($step['Status']) == 'new') {
                    $isFinished = false;
                }
            }
        }

        return $isFinished;
    }
}
