<?php

namespace VR\AppBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LogsFilterData
 *
 * @package VR\AppBundle\Form
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 */
class LogsFilterData
{
    /**
     * @Assert\NotBlank()
     */
    public $filename;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     * @var \DateTime
     */
    public $date;

    public $type;

    public $search;
}