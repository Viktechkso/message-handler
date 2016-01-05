<?php

namespace VR\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Datamap
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 * @author Michał Jabłoński <mjapko@gmail.com>
 *
 * @ORM\Entity(repositoryClass="VR\AppBundle\Entity\Repository\DatamapRepository")
 * @ORM\Table(name="datamaps")
 */
class Datamap
{
    const TYPE_DATA = 0;
    const TYPE_SEARCH = 1;
    const TYPE_EMAIL = 2;

    public static $types = [
        self::TYPE_DATA => 'Data',
        self::TYPE_SEARCH => 'Search',
        self::TYPE_EMAIL => 'Email',
    ];

	/**
	 * @ORM\Id
	 * @ORM\Column(type="bigint")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

    /**
     * @ORM\Column(name="name", type="string", unique=true, length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="type", type="integer")
     */
    protected $type;

    /**
     * Contains JSON with the datamap
     *
     * @ORM\Column(name="map", type="text")
     */
    protected $map;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

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
     * @return string
     */
    public function getMap()
    {
        return $this->map;
    }

    public function getMapArray()
    {
        $mapArray = json_decode($this->map, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON parsing error: ' . json_last_error_msg());
        }

        return $mapArray;
    }

    /**
     * @param string $map
     * @return $this
     */
    public function setMap($map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name string
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return isset(self::$types[$this->type]) ? self::$types[$this->type] : null;
    }


    /**
     * @param $type integer
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validateMap(ExecutionContextInterface $context)
    {
        json_decode($this->getMap(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $context->buildViolation('JSON parsing error: ' . json_last_error_msg())
                ->atPath('map')
                ->addViolation();
        }
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
}
