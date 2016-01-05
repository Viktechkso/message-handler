<?php

namespace VR\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VR\AppBundle\Entity\Message;
use VR\AppBundle\Form\MessageRunAtType;
use VR\AppBundle\Form\MessageSearchData;
use VR\AppBundle\Form\MessageSearchType;

/**
 * Message controller.
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 * @author Andrzej Prusinowski <andrzej@avris.it>
 *
 * @Route("/message")
 */
class MessageController extends Controller
{
    /**
     * Lists all Message entities.
     *
     * @Route("/", name="message")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $searchForm = $this->createSearchForm();
        $searchForm->handleRequest($request);

        $query = $em->getRepository('VRAppBundle:Message')->searchQB($searchForm->getData());

        $this->get('session')->set('message_search_form', serialize($searchForm->getData()));

        if ($request->get('items')) {
            $this->setItemsPerPageInSession($request->get('items'));
        }

        $itemsPerPage = $this->getItemsPerPageFromSession();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            $itemsPerPage,
            array(
                'defaultSortFieldName' => 'm.id',
                'defaultSortDirection' => 'asc'
            )
        );

        return array(
            'entities' => $pagination,
            'searchForm' => $searchForm->createView(),
            'messageTypes' => $this->get('vr.plugin_manager')->getAvailableMessageTypes(),
            'itemsPerPage' => $itemsPerPage
        );
    }

    public function getItemsPerPageFromSession()
    {
        return $this->get('session')->get('messages.list.items_per_page', 50);
    }

    protected function setItemsPerPageInSession($itemsPerPage)
    {
        $this->get('session')->set('messages.list.items_per_page', $itemsPerPage);
    }

    /**
     * @Route("/search-clear", name="message_search_clear")
     */
    public function showMessageSearchClear()
    {
        $this->get('session')->set('message_search_form', null);

        return $this->redirectToRoute('message');
    }

    /**
     * @Route("/batch", name="message_batch")
     * @Method("POST")
     */
    public function batchAction(Request $request)
    {
        $ids = $request->get('batch_ids');
        $action = $request->get('action');
        $actionType = $request->get('action_type');

        if (!$action) {
            $this->get('session')->getFlashBag()->add('error', 'Please select batch action.');

            return $this->redirectToRoute('message');
        }

        $allowedActions = ['run', 'new', 'halt', 'cancel', 'reset_guids'];

        if (!in_array($action, $allowedActions)) {
            throw new \Exception('This action is not allowed.');
        }

        if (!$actionType) {
            $this->get('session')->getFlashBag()->add('error', 'Please select batch action type.');

            return $this->redirectToRoute('message');
        }

        if ($action == 'selected' && empty($ids)) {
            $this->get('session')->getFlashBag()->add('error', 'Please select at least one Message.');

            return $this->redirectToRoute('message');
        }

        $em = $this->getDoctrine()->getManager();

        switch ($actionType) {
            case 'selected':
                $messages = $em->getRepository('VRAppBundle:Message')->findById($ids);
                break;
            case 'all':
                $searchFormData = unserialize($this->get('session')->get('message_search_form'));

                if (!$searchFormData) {
                    $searchFormData = new MessageSearchData();
                }

                $query = $em->getRepository('VRAppBundle:Message')->searchQB($searchFormData);
                $messages = $query->getQuery()->getResult();
                break;
            default:
                throw new \InvalidArgumentException('Invalid action type "' . $actionType . '".');
        }

        $counter = $this->batchMessages($action, $messages);

        $this->get('session')->getFlashBag()->add('success', 'Batch action executed on ' . $counter . ' items.');

        return $this->redirectToRoute('message');
    }

    private function batchMessages($action, $messages)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($action) {
            case 'new':
                foreach ($messages as $message) {
                    if (strtolower($message->getFlowStatus()) != 'new') {
                        $message->batchChangeStepStatuses(null, 'New');
                        $message->setFlowStatus('New');
                        $em->persist($message);
                    }
                }

                $em->flush();
                break;
            case 'halt':
                foreach ($messages as $message) {
                    if (strtolower($message->getFlowStatus()) != 'halted') {
                        $message->setFlowStatus('Halted');
                        $em->persist($message);
                    }
                }

                $em->flush();
                break;
            case 'cancel':
                foreach ($messages as $message) {
                    if (strtolower($message->getFlowStatus()) != 'cancelled') {
                        $message->setFlowStatus('Cancelled');
                        $em->persist($message);
                    }
                }

                $em->flush();
                break;
            case 'reset_guids':
                foreach ($messages as $message) {
                    $message->resetGuids();
                    $em->persist($message);
                }

                $em->flush();
                break;
            default:
                throw new \Exception('Please select correct batch action.');
        }

        return count($messages);
    }

    /**
     * Finds and displays a Message entity.
     *
     * @Route("/{id}", name="message_show")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $config = $this->container->getParameter('vr_app');
        $em = $this->getDoctrine()->getManager();
        $stepsManager = $this->get('vr.steps_manager');

        $message = $em->getRepository('VRAppBundle:Message')->find($id);

        if (!$message) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }

        try {
            $steps = $message->getStepsArray();
        } catch (\Exception $e) {
            $steps = null;
            $stepsError = $e->getMessage();
        }

        $stepsManager->setStepsArray($steps);

        $stepChanges = $em->getRepository('VRAppBundle:StepChange')->findOrderedByMessage($message);

        $runAtForm = $this->createForm(new MessageRunAtType(), $message);

        $runAtForm->handleRequest($request);

        if ($runAtForm->isSubmitted()) {
            if ($runAtForm->isValid()) {
                $message = $runAtForm->getData();
                $em->persist($message);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'The Message is set to run after specified time.');

                return $this->redirect($request->headers->get('referer'));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Please set correct value for "Run at" field.');
            }
        }

        return array(
            'entity' => $message,
            'steps' => $steps,
            'stepsError' => isset($stepsError) ? $stepsError : null,
            'currentStep' => $stepsManager->getCurrentStepNumber(),
            'nextStep' => $stepsManager->getNextStepNumber(),
            'countAllSteps' => $stepsManager->countAllSteps(),
            'countCompletedSteps' => $stepsManager->countCompletedSteps(),
            'completedStepsPercentage' => $stepsManager->getCompletedStepsPercentage(),
            'sugarcrmUrl' => $config['sugarcrm_url'],
            'stepChanges' => $stepChanges,
            'runAtForm' => $runAtForm->createView(),
            'messageTypes' => $this->get('vr.plugin_manager')->getAvailableMessageTypes(),
        );
    }

    /**
     * @Route("/set-status/{id}/{status}", name="message_set_status")
     * @Method("GET")
     *
     * @param Request $request
     * @param $id
     * @param $status
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function setStatusAction(Request $request, $id, $status)
    {
        if (!in_array($status, Message::$allowedStatuses)) {
            throw new \Exception('This status is not allowed.');
        }

        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('VRAppBundle:Message')->find($id);

        if (!$message) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }

        if (strtolower($status) == 'new') {
            $message->batchChangeStepStatuses(null, 'New');
        }

        $message->setFlowStatus($status);
        $em->persist($message);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'Message status changed to "' . $status . '".');

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/run-message/{id}", name="message_run")
     * @Method("GET")
     *
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function runMessageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('VRAppBundle:Message')->find($id);

        if (!$message) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }

        $message->setForced(true);
        $message->batchChangeStepStatuses(null, 'New');
        $message->setFlowStatus('New');
        $em->persist($message);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            'Message set as "Forced" successfully. Waiting for the parser...'
        );

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/reset-guids/{id}", name="message_reset_guids")
     * @Method("GET")
     *
     * @param Request $request
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resetGuidsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('VRAppBundle:Message')->find($id);

        if (!$message) {
            throw $this->createNotFoundException('Unable to find Message entity.');
        }

        $message->resetGuids();
        $em->persist($message);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            'GUIDs reset successfully for message with ID: "' . $id . '".'
        );

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/ajax/list-status/{id}", name="message_ajax_list_status")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxListStatus(Request $request)
    {
        return new Response('AJAX RESPONSE');
    }

    /**
     * @Route("/ajax/list-actions/{id}", name="message_ajax_list_actions")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxListActions(Request $request)
    {
        return new Response('AJAX RESPONSE');
    }

    /**
     * @Route("/ajax/status-box", name="message_ajax_status_box")
     * @Template("VRAppBundle:Message:ajax_status_box.html.twig")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxStatusBoxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $statusCounters = $em->getRepository('VRAppBundle:Message')->getStatusCounters();

        return array(
            'statusCounters' => $statusCounters
        );
    }

    /**
     * @Route("/set-step-guid/{id}/{stepNumber}", name="message_set_step_guid")
     *
     * @param Request $request
     * @param Message $message
     * @param integer $stepNumber
     *
     * @throws \Exception
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setStepGuidAction(Request $request, Message $message, $stepNumber)
    {
        $guid = $request->get('guid');
        $newGuid = $request->get('new-guid-value');

        if (!$guid) {
            $this->get('session')->getFlashBag()->add('error', 'Error setting GUID. Form is not valid.');

            return $this->redirect($request->headers->get('referer'));
        }

        if ($guid == 'new-guid') {
            if ($newGuid) {
                $guid = $newGuid;
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Please provide new GUID value.');

                return $this->redirect($request->headers->get('referer'));
            }
        }

        $em = $this->getDoctrine()->getManager();

        $message->changeStepParameter($stepNumber, 'GUID', $guid);
        $message->batchChangeStepStatuses(null, 'New');
        $message->setFlowStatus('New');
        $em->persist($message);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'GUID set successfully in step number ' . $stepNumber);

        return $this->redirect($request->headers->get('referer'));
    }

    private function createSearchForm()
    {
        $em = $this->getDoctrine()->getManager();
        $pluginManager = $this->get('vr.plugin_manager');

        $searchFormData = unserialize($this->get('session')->get('message_search_form'));

        if (!$searchFormData) {
            $searchFormData = new MessageSearchData();
        }

        $statuses = $em->getRepository('VRAppBundle:Message')->getStatusesForSearch();

        $searchForm = $this->createForm(
            new MessageSearchType($pluginManager->getAvailableMessageTypes(), $statuses),
            $searchFormData
        );

        return $searchForm;
    }
}
