<?php

namespace VR\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MessageRunAtType
 *
 * @package VR\AppBundle\Form
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class MessageRunAtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('runAt');
    }

    public function getName()
    {
        return 'run_at';
    }
}
