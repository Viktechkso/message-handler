<?php

namespace VR\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AccountCreateType
 *
 * @package VR\AppBundle\Form
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class AccountCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vatCountry', 'choice', array(
                'choices' => ['DK' => 'DK'],
                'required' => true
            ))
            ->add('vatNumber', 'text', array(
                'required' => true
            ));
    }

    public function getName()
    {
        return 'create_account';
    }
}
