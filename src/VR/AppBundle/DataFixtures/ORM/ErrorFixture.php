<?php
namespace VR\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;
use VR\AppBundle\Entity\Error;
use VR\AppBundle\DataFixtures\FixturesOrdering;

/**
 * Error
 *
 * @author Michał Jabłoński <mjapko@gmail.com>
 */
class ErrorFixture extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Loads fixtures.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 500; $i++) {
            $error = new Error();
            $error->setMessage($this->getReference('message-' . ($i % 50 + 1)));
            $error->setErrorMessage('Simple error message in error ID = ' . $i);
            $error->setErrorPayload(json_encode([
                ['id' => '123-456-789'],
                ['id' => '987-654-321']
            ]));
            $error->setEntryAt(new \DateTime('-' . rand(1, 100) . ' min'));
            $error->setStepNo(2);

            $manager->persist($error);
            $this->addReference('error-' . $i, $error);
        }

        $manager->flush();
    }

    /**
     * Returns fixtures class ordering.
     *
     * @return int
     */
    public function getOrder()
    {
        return FixturesOrdering::getOrdering('Error');
    }
}