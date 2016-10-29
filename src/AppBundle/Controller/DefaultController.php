<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Guest;
use AppBundle\Entity\Repository\GuestRepository;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need

        $name = $request->get('name');

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var GuestRepository $repo */
        $repo = $em->getRepository('AppBundle\Entity\Guest');

        $users = $repo->getAllWithNameShorterThen();

        /** @var Guest $user */
        foreach ($users as $user) {
            $user->setCategory($em->find('AppBundle\Entity\GuestCategory', 1));

            $em->persist($user);
        }

        $em->flush();

        return $this->render('default/index.html.twig',
            array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'guests' => $users
        ));
    }
}
