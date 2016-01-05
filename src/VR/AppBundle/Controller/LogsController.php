<?php

namespace VR\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use VR\AppBundle\Form\LogsFilterData;
use VR\AppBundle\Form\LogsFilterType;
use VR\AppBundle\Service\LogReader;

/**
 * @author Andrzej Prusinowski <andrzej@avris.it>
 *
 * @Route("/logs")
 */
class LogsController extends Controller
{
    /**
     * @Route("/", name="logs_read")
     */
    public function readAction(Request $request)
    {
        /** @var LogReader $reader */
        $reader = $this->get('vr.log_reader');
        $filter = new LogsFilterData;
        $filter->date = new \DateTime;
        $logs = [];

        $form = $this->createForm(new LogsFilterType, $filter, ['files' => $reader->getFileList() ]);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid()) {
                $logs = $reader->read($filter);
            }
        }

        return $this->render('VRAppBundle:Logs:read.html.twig', array(
            'form' => $form->createView(),
            'logs' => $logs,
        ));
    }
}
