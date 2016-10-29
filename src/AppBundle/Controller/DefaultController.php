<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Guest;
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

        $guest1 = new Guest();

        $guest1->setName($name);

        $em->persist($guest1);

        $em->flush();

        return $this->render('default/index.html.twig',
            array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'name' => $name
        ));
    }
}
