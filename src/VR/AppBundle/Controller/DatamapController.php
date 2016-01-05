<?php

namespace VR\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use VR\AppBundle\Entity\Datamap;
use VR\AppBundle\Entity\Message;
use VR\AppBundle\Form\DatamapType;
use VR\DataMapperBundle\DataMapper\DataMapper;
use VR\DataMapperBundle\DataMapper\Map;

/**
 * @author Andrzej Prusinowski <andrzej@avris.it>
 * @author Michał Jabłoński <mjapko@gmail.com>
 *
 * @Route("/datamap")
 */
class DatamapController extends Controller
{
    /**
     * @Route("/", name="datamap_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $datamaps = $em->getRepository('VRAppBundle:Datamap')->findAll();

        return $this->render('VRAppBundle:Datamap:list.html.twig', array(
            'datamaps' => $datamaps
        ));
    }

    /**
     * @Route("/new", name="datamap_new")
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(new DatamapType(), new Datamap());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $datamap = $form->getData();
            $em->persist($datamap);
            $em->flush();

            return $this->redirectToRoute('datamap_list');
        }

        return $this->render('VRAppBundle:Datamap:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/edit/{id}", name="datamap_edit")
     */
    public function editAction(Request $request, Datamap $datamap)
    {
        $form = $this->createForm(new DatamapType(), $datamap);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $datamap = $form->getData();
            $em->persist($datamap);
            $em->flush();

            return $this->redirectToRoute('datamap_list');
        }

        return $this->render('VRAppBundle:Datamap:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/test/{message}/{stepNumber}", name="datamap_test")
     */
    public function testAction(Message $message, $stepNumber)
    {
        $steps = json_decode($message->getFlow(), true);
        if (!isset($steps[$stepNumber])) {
            throw new NotFoundHttpException;
        }
        $step = $steps[$stepNumber];

        $datamap = $this->getDoctrine()->getManager()->getRepository('VRAppBundle:Datamap')->findOneByName($step['Datamap']);
        if (!$datamap) {
            throw new NotFoundHttpException;
        }

        $data = json_decode($message->getFlowMessage());
        if ($message->getFlowName() == 'nne') {
            $data = $data[$stepNumber - 1];
        }

        $map = new Map(json_decode($datamap->getMap(), true));
        $dataMapper = new DataMapper();
        $dataMapper->setMap($map);

        return $this->render('VRAppBundle:Datamap:test.html.twig', array(
            'message' => json_encode($data, JSON_PRETTY_PRINT),
            'datamap' => $datamap,
            'output' => json_encode($dataMapper->map($data), JSON_PRETTY_PRINT),
        ));
    }

    /**
     * @Route("/list/json", name="datamap_list_json")
     *
     * @return JsonResponse
     */
    public function listJsonAction()
    {
        $em = $this->getDoctrine()->getManager();

        $datamaps = $em->getRepository('VRAppBundle:Datamap')->findAllForJsonList();

        return new JsonResponse($datamaps);
    }
}
