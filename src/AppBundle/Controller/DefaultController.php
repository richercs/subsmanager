<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserAccount;
use AppBundle\Entity\Subscription;
use AppBundle\Repository\UserAccountRepository;
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

        $user_account = new UserAccount();

        $user_account->setFirstName($name);

        $user_account->setLastName(strrev($name));

        $em->persist($user_account);

        $em->flush();

        /** @var UserAccountRepository $user_repo */
        $user_repo = $em->getRepository('AppBundle\Entity\UserAccount');

        $users = $user_repo->getAllWithNameShorterThen();

        /** @var UserAccount $user */
        foreach ($users as $user) {
            $user->setLastName("rövid volt");

            $em->persist($user);
        }

        $em->flush();

        return $this->render('default/index.html.twig',
            array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'users' => $users
        ));
    }
}
