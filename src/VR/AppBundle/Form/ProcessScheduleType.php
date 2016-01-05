<?php

namespace VR\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use VR\AppBundle\Plugin\PluginManager;

/**
 * Class ProcessScheduleType
 *
 * @package VR\AppBundle\Form
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ProcessScheduleType extends AbstractType
{
    /** @var PluginManager */
    protected $pluginManager;

    public function __construct($pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add('minute', 'text', [
                'required' => false
            ])
            ->add('hour', 'text', [
                'required' => false
            ])
            ->add('dayOfMonth', 'text', [
                'required' => false
            ])
            ->add('month', 'text', [
                'required' => false
            ])
            ->add('dayOfWeek', 'text', [
                'required' => false
            ])
            ->add('type', 'choice', [
                'choices' => [
                    'Collectors' => $this->pluginManager->getCollectorsRunModesList(),
                    'Workers' => $this->pluginManager->getWorkersRunModesList()
                ]
            ])
            ->add('parameters', 'textarea', [
                'required' => false
            ])
            ->add('description', 'textarea', [
                'required' => false
            ])
            ->add('enabled', 'checkbox', [
                'required' => false
            ])
        ;
	}

	public function getName()
	{
		return 'process_schedule';
	}
}
