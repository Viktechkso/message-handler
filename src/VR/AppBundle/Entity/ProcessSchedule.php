<?php

namespace VR\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use VR\AppBundle\Plugin\PluginManager;
use VR\AppBundle\Service\CronHelper;

/**
 * @author Michał Jabłoński <mjapko@gmail.com>
 * @author Andrzej Prusinowski <andrzej@avris.it>
 *
 * @ORM\Entity(repositoryClass="VR\AppBundle\Entity\Repository\ProcessScheduleRepository")
 * @ORM\Table(name="process_schedules")
 */
class ProcessSchedule
{
    public static $sqlTypes = [
        'collector.sql.main',
        'collector.sql.Duplet'
    ];

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=25)
     * @Assert\NotBlank(message="'Day of Week' value should not be blank.")
     * @Assert\Regex(pattern="/^[-0-9,\/*$]+/", message="'Day of Week' value is not valid.")
     */
    protected $dayOfWeek;

    /**
     * @ORM\Column(type="string", nullable=true, length=25)
     * @Assert\NotBlank(message="'Month' value should not be blank.")
     * @Assert\Regex(pattern="/^[-0-9,\/*$]+/", message="'Month' value is not valid.")
     */
    protected $month;

    /**
     * @ORM\Column(type="string", nullable=true, length=25)
     * @Assert\NotBlank(message="'Day of Month' value should not be blank.")
     * @Assert\Regex(pattern="/^[-0-9,\/*$]+/", message="'Day of Month' value is not valid.")
     */
    protected $dayOfMonth;

    /**
     * @ORM\Column(type="string", nullable=true, length=25)
     * @Assert\NotBlank(message="'Hour' value should not be blank.")
     * @Assert\Regex(pattern="/^[-0-9,\/*$]+/", message="'Hour' value is not valid.")
     */
    protected $hour;

    /**
     * @ORM\Column(type="string", nullable=true, length=25)
     * @Assert\NotBlank(message="'Minute' value should not be blank.")
     * @Assert\Regex(pattern="/^[-0-9,\/*$]+/", message="'Minute' value is not valid.")
     */
    protected $minute;

    /**
     * @ORM\Column(type="string", length=127)
     */
    protected $type;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $parameters;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastRunAt;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->enabled = true;

        $this->minute = '*';
        $this->hour = '*';
        $this->dayOfMonth = '*';
        $this->month = '*';
        $this->dayOfWeek = '*';
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
     * Set dayOfWeek
     *
     * @param string $dayOfWeek
     * @return ProcessSchedule
     */
    public function setDayOfWeek($dayOfWeek)
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    /**
     * Get dayOfWeek
     *
     * @return string
     */
    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    /**
     * Set month
     *
     * @param string $month
     * @return ProcessSchedule
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return string
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set dayOfMonth
     *
     * @param string $dayOfMonth
     * @return ProcessSchedule
     */
    public function setDayOfMonth($dayOfMonth)
    {
        $this->dayOfMonth = $dayOfMonth;

        return $this;
    }

    /**
     * Get dayOfMonth
     *
     * @return string
     */
    public function getDayOfMonth()
    {
        return $this->dayOfMonth;
    }

    /**
     * Set hour
     *
     * @param string $hour
     * @return ProcessSchedule
     */
    public function setHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * Get hour
     *
     * @return string
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set minute
     *
     * @param string $minute
     * @return ProcessSchedule
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;

        return $this;
    }

    /**
     * Get minute
     *
     * @return string
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ProcessSchedule
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set parameters
     *
     * @param string $parameters
     * @return ProcessSchedule
     */
    public function setParameters($parameters)
    {
        if (in_array($this->getType(), self::$sqlTypes)) {
            $this->parameters = json_encode(['sql' => $parameters]);
        } else {
            $this->parameters = $parameters;
        }

        return $this;
    }

    /**
     * Get parameters
     *
     * @return string 
     */
    public function getParameters($pure = false)
    {
        if (in_array($this->getType(), self::$sqlTypes) && !$pure) {
            $parameters = json_decode($this->parameters, true);
            return $parameters['sql'];
        } else {
            return $this->parameters;
        }
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ProcessSchedule
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function canBeRunAt(\DateTime $dateTime)
    {
        return (
            (in_array((int) $dateTime->format('w'), CronHelper::getCronPossibilities($this->dayOfWeek))) &&
            (in_array((int) $dateTime->format('n'), CronHelper::getCronPossibilities($this->month))) &&
            (in_array((int) $dateTime->format('j'), CronHelper::getCronPossibilities($this->dayOfMonth))) &&
            (in_array((int) $dateTime->format('G'), CronHelper::getCronPossibilities($this->hour))) &&
            (in_array((int) $dateTime->format('i'), CronHelper::getCronPossibilities($this->minute)))
        );
    }

    public function getTime()
    {
        return trim($this->minute . ' ' . $this->hour . ' ' . $this->dayOfMonth . ' ' . $this->month . ' ' . $this->dayOfWeek);
    }

    public function getTypeName()
    {
        return isset(PluginManager::$runModeNames[$this->getType()]) ? PluginManager::$runModeNames[$this->getType()] : null;
    }

    /**
     * @Assert\Callback
     */
    public function validateParameters(ExecutionContextInterface $context)
    {
        if (!in_array($this->getType(), self::$sqlTypes) && $this->getParameters(true) && !$this->checkParametersJsonStructure()) {
            $context->buildViolation('Provided JSON structure is not valid.')
                ->atPath('parameters')
                ->addViolation();
        }

        $parameters = $this->getParametersArray();

        //@todo: check parameters provided by plugins
//        if (isset(ProcessManager::$processTypeParameters[$this->getType()])) {
//            $parameterSchema = ProcessManager::$processTypeParameters[$this->getType()];
//            foreach ($parameterSchema as $name => $value) {
//                if ($value['required'] && !isset($parameters[$name])) {
//                    $context->buildViolation(sprintf('Please provide %s parameter.', $name))
//                        ->atPath('parameters')
//                        ->addViolation();
//                }
//            }
//        }
    }

    protected function checkParametersJsonStructure()
    {
        json_decode($this->getParameters(true));

        return (json_last_error() !== JSON_ERROR_NONE) ? false : true;
    }

    public function getParametersArray()
    {
        if (in_array($this->getType(), self::$sqlTypes)) {
            return ['sql' => $this->parameters];
        }

        return $this->getParameters() ? json_decode($this->getParameters(true), true) : [];
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastRunAt()
    {
        return $this->lastRunAt;
    }

    /**
     * @param mixed $lastRunAt
     */
    public function setLastRunAt($lastRunAt)
    {
        $this->lastRunAt = $lastRunAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function enable()
    {
        $this->setEnabled(true);
    }

    public function disable()
    {
        $this->setEnabled(false);
    }
}
