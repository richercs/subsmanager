<?php

namespace AppBundle\Controller;

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

        return $this->render('default/index.html.twig',
            array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'name' => $name
        ));
    }
}
