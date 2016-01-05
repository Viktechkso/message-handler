<?php

namespace VR\AppBundle\Form;

/**
 * Class MessageSearchData
 *
 * @package VR\AppBundle\Form
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class MessageSearchData
{
    public $flowName;

    public $flowStatuses;

    /**
     * @var \DateTime
     */
    public $createdAtFrom;

    /**
     * @var \DateTime
     */
    public $createdAtTo;

    public $containing;
}