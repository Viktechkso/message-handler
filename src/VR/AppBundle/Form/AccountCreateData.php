<?php

namespace VR\AppBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AccountCreateData
 *
 * @package VR\AppBundle\Form
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class AccountCreateData
{
    /**
     * @Assert\NotBlank()
     */
    public $vatCountry;

    /**
     * @Assert\NotBlank()
     * @Assert\NotEqualTo(value = "00000000")
     */
    public $vatNumber;
}