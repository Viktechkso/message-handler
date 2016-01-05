<?php

namespace VR\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class LogsFilterType
 *
 * @package VR\AppBundle\Form
 *
 * @author Andrzej Prusinowski <andrzej@avris.it>
 */
class LogsFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filename', 'choice', [
                'choices' => ['' => ''] + $options['files'],
                'required' => true,
            ])
            ->add('date', 'date', [
                'required' => true,
                'format' => 'yyyy MM dd',
                'years' => range(2014, date('Y') + 5),
            ])
            ->add('type', 'text', ['required' => false])
            ->add('search', 'text', ['required' => false]);
	}

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'files' => [],
        ));
    }

    public function getName()
    {
        return 'logs_filter';
    }
}
