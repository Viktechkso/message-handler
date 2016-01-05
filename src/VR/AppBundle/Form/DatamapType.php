<?php

namespace VR\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use VR\AppBundle\Entity\Datamap;

/**
 * Class DatamapType
 *
 * @package VR\AppBundle\Form
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class DatamapType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', 'choice', [
                'choices' => array_combine(Datamap::$types, Datamap::$types),
            ])
            ->add('map', 'textarea')
            ->add('description', 'textarea')
        ;
    }

    public function getName()
    {
        return 'datamap';
    }
}
