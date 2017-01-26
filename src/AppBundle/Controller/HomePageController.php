<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomePageController extends Controller
{
    /**
     * @Route("/homepage", name="homepage")
     *
     * @param Request request
     * @return array
     */
    public function showAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $user_repo */
        $user_repo = $em->getRepository('AppBundle\Entity\UserAccount');

        $users = $user_repo->findAll();

        return $this->render('default/index.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'users' => $users
            ));
    }

}