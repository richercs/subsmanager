<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\UserAccountType;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserAccountController extends Controller
{
    /**
     * @Route("/useraccount_list_all", name="useraccount_list_all")
     *
     * @param Request request
     * @return array
     */
    public function listAllUserAccountsAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $user_repo */
        $user_repo = $em->getRepository('AppBundle\Entity\UserAccount');

        $users = $user_repo->findAll();

        return $this->render('users/listAllUserAccounts.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'users' => $users
            ));
    }

    /**
     * @Route("/useraccount_add_user", name="useraccount_add_user")
     *
     * @param Request request
     * @return array
     */
    public function addUserAccountAction(Request $request)
    {
//        /** @var UserAccount $loggedInUser */
//        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $new_user = new UserAccount();

        $form = $this->createForm(new UserAccountType(), $new_user);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($new_user);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('useraccount_add_user');
        }

        return $this->render('users/addUserAccount.html.twig',
            array(
                'new_user' => $new_user,
                'form' => $form->createView()
            ));

    }


}