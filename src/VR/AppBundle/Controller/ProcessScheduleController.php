<?php

namespace VR\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use VR\AppBundle\Entity\ProcessSchedule;
use VR\AppBundle\Form\ProcessScheduleType;
use VR\AppBundle\Plugin\PluginManager;

/**
 * @author Michał Jabłoński <mjapko@gmail.com>
 * @author Andrzej Prusinowski <andrzej@avris.it>
 *
 * @Route("/schedules")
 */
class ProcessScheduleController extends Controller
{
    /**
     * @Route("/", name="process_schedule_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $processSchedules = $em->getRepository('VRAppBundle:ProcessSchedule')->findAll();

        return $this->render('VRAppBundle:ProcessSchedule:list.html.twig', array(
            'processSchedules' => $processSchedules
        ));
    }

    /**
     * @Route("/new", name="process_schedule_new")
     */
    public function newAction(Request $request)
    {
        $pluginManager = $this->get('vr.plugin_manager');

        $form = $this->createForm(new ProcessScheduleType($pluginManager), new ProcessSchedule());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $processSchedule = $form->getData();
            $em->persist($processSchedule);
            $em->flush();

            return $this->redirectToRoute('process_schedule_list');
        }

        return $this->render('VRAppBundle:ProcessSchedule:new.html.twig', array(
            'form' => $form->createView(),
            'parameters' => $pluginManager->getAllPluginsParameters(),
        ));
	}

    /**
     * @Route("/edit/{id}", name="process_schedule_edit")
     */
    public function editAction(Request $request, ProcessSchedule $processSchedule)
    {
        $pluginManager = $this->get('vr.plugin_manager');

        $form = $this->createForm(new ProcessScheduleType($pluginManager), $processSchedule);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $processSchedule = $form->getData();
            $em->persist($processSchedule);
            $em->flush();

            return $this->redirectToRoute('process_schedule_list');
        }

        return $this->render('VRAppBundle:ProcessSchedule:edit.html.twig', array(
            'form' => $form->createView(),
            'parameters' => $pluginManager->getAllPluginsParameters(),
        ));
    }

    /**
     * @Route("/delete/{id}", name="process_schedule_delete")
     */
    public function deleteAction(Request $request, ProcessSchedule $processSchedule)
    {
        $em = $this->getDoctrine()->getManager();
        
        $em->remove($processSchedule);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Scheduled process successfully removed.');
        
        return $this->redirectToRoute('process_schedule_list');
    }

    /**
     * @Route("/enable/{id}", name="process_schedule_enable")
     */
    public function enableAction(Request $request, ProcessSchedule $processSchedule)
    {
        $em = $this->getDoctrine()->getManager();

        $processSchedule->enable();
        $em->persist($processSchedule);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Scheduled process successfully enabled.');

        return $this->redirectToRoute('process_schedule_list');
    }

    /**
     * @Route("/disable/{id}", name="process_schedule_disable")
     */
    public function disableAction(Request $request, ProcessSchedule $processSchedule)
    {
        $em = $this->getDoctrine()->getManager();

        $processSchedule->disable();
        $em->persist($processSchedule);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Scheduled process successfully disabled.');

        return $this->redirectToRoute('process_schedule_list');
    }

    /**
     * @Route("/run/{id}", name="process_schedule_run")
     */
    public function runAction(ProcessSchedule $scheduledProcess)
    {
        $em = $this->getDoctrine()->getManager();

        $scheduledProcess->setLastRunAt(new \DateTime());
        $em->persist($scheduledProcess);
        $em->flush();

        $this->get('vr.plugin_manager')->runScheduledProcess($scheduledProcess);

        $this->get('session')->getFlashBag()->add('success', 'SQL has been executed.');

        return $this->redirectToRoute('process_schedule_list');
    }
}
