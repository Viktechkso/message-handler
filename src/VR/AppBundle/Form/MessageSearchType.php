<?php

namespace VR\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use VR\AppBundle\Entity\Message;

/**
 * Class MessageSearchType
 *
 * @package VR\AppBundle\Form
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class MessageSearchType extends AbstractType
{
    protected $availableMessageTypes;
    protected $statuses;

    public function __construct($availableMessageTypes, $statuses)
    {
        $this->availableMessageTypes = $availableMessageTypes;
        $this->statuses = $statuses;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('flowName', 'choice', [
                'choices' => $this->availableMessageTypes,
                'required' => false
            ])
            ->add('flowStatuses', 'choice', [
                'choices' => $this->statuses,
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ])
            ->add('createdAtFrom', 'datetime', [
                'required' => false
            ])
            ->add('createdAtTo', 'datetime', [
                'required' => false
            ])
            ->add('containing', 'text', [
                'required' => false
            ])
        ;
    }

    public function getName()
    {
        return 'message_search';
    }
}
