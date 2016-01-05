<?php
namespace Helper;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Functional extends \Codeception\Module
{
    /**
     * Retrieves some Message IDs from database.
     *
     * @throws \Codeception\Exception\Module
     */
    public function getMessageIds($limit)
    {
        $container = $this->getModule('Symfony2')->container;
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();

        $results = $em->getRepository('VRAppBundle:Message')->createQueryBuilder('m')
            ->select('m.id')
            ->setMaxResults($limit)
            ->getQuery()
            ->getScalarResult();

        $ids = [];

        if (count($results)) {
            foreach ($results as $result) {
                $ids[] = $result['id'];
            }
        }

        return $ids;
    }

    public function mockMuleConnectorServiceForVatStatus()
    {
        $container = $this->getModule('Symfony2')->container;

        $mock = \Mockery::mock('overload:VR\AppBundle\Service\MuleConnector');
        $mock->shouldReceive('checkVatStatus')->andReturn([
            'total' => 1,
            'success' => 2,
            'duplicate' => 3,
            'error' => 4,
            'unknown' => 1
        ]);

        $container->set('vr.mule_connector', $mock);
    }
}
