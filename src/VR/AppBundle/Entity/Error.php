<?php

namespace VR\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Error
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 *
 * @ORM\Entity(repositoryClass="VR\AppBundle\Entity\Repository\ErrorRepository")
 * @ORM\Table(name="errors")
 */
class Error
{
    const STATUS_NEW = 'New';
    const STATUS_READY = 'Ready';
    const STATUS_IN_PROGRESS = 'In progress';
    const STATUS_ERROR = 'Error';
    const STATUS_DONE = 'Done';

	/**
	 * @ORM\Id
	 * @ORM\Column(type="bigint")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="errors")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $message;

    /**
     * @ORM\Column(name="step_no", type="bigint")
     */
    protected $stepNo;

	/**
	 * @ORM\Column(name="error_message", type="text")
	 */
	protected $errorMessage;

    /**
     * @ORM\Column(name="entry_ts", type="datetime")
     */
    protected $entryAt;

    /**
     * @ORM\Column(name="error_payload", type="text", nullable=true)
     */
    protected $errorPayload;

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
     * Set errorMessage
     *
     * @param string $errorMessage
     * @return Error
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * Get errorMessage
     *
     * @return string 
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Set entryAt
     *
     * @param \DateTime $entryAt
     * @return Error
     */
    public function setEntryAt($entryAt)
    {
        $this->entryAt = $entryAt;

        return $this;
    }

    /**
     * Get entryAt
     *
     * @return \DateTime 
     */
    public function getEntryAt()
    {
        return $this->entryAt;
    }

    /**
     * Set message
     *
     * @param \VR\AppBundle\Entity\Message $message
     * @return Error
     */
    public function setMessage(\VR\AppBundle\Entity\Message $message = null)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \VR\AppBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getStepNo()
    {
        return $this->stepNo;
    }

    /**
     * @param mixed $stepNo
     *
     * @return $this
     */
    public function setStepNo($stepNo)
    {
        $this->stepNo = $stepNo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrorPayload()
    {
        return $this->errorPayload;
    }

    /**
     * @param mixed $errorPayload
     *
     * @return $this
     */
    public function setErrorPayload($errorPayload)
    {
        $this->errorPayload = $errorPayload;

        return $this;
    }

    public function getErrorPayloadIds()
    {
        $payload = json_decode($this->getErrorPayload(), true);

        if (!$payload) return [];

        # legacy - for old payloads without "module" key
        $items = isset($payload['ids']) ? $payload['ids'] : $payload;

        $ids = [];

        if (count($items)) {
            foreach ($items as $item) {
                $ids[] = $item['id'];
            }
        }

        return $ids;
    }

    public function getErrorPayloadModule()
    {
        $payload = json_decode($this->getErrorPayload(), true);

        if (!$payload) return [];

        return isset($payload['module']) ? $payload['module'] : null;
    }

    public function getShowCreateNew()
    {
        $payload = json_decode($this->getErrorPayload(), true);

        if (!$payload) return true;

        return isset($payload['showCreateNew']) ? $payload['showCreateNew'] : true;
    }

    public function getShowUseThis()
    {
        $payload = json_decode($this->getErrorPayload(), true);

        if (!$payload) return true;

        return isset($payload['showUseThis']) ? $payload['showUseThis'] : true;
    }

    public function getShowButtons()
    {
        $payload = json_decode($this->getErrorPayload(), true);

        if (!$payload) return true;

        return isset($payload['showButtons']) ? $payload['showButtons'] : true;
    }

    public function getShowRadioButtons()
    {
        $payload = json_decode($this->getErrorPayload(), true);

        if (!$payload) return true;

        return isset($payload['showRadioButtons']) ? $payload['showRadioButtons'] : true;
    }
}
