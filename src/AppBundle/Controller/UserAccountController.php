<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserContact;
use AppBundle\Repository\UserContactRepository;
use FOS\UserBundle\Util\PasswordUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\UserAccountType;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserAccountController extends Controller
{
    /**
     * @Route("/useraccount/list_all", name="useraccount_list_all")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function listAllUserAccountsAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $user_repo */
        $user_repo = $em->getRepository('AppBundle\Entity\UserAccount');

        $users = $user_repo->findAll();

        return $this->render('users/listAllUserAccounts.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'users' => $users,
                'logged_in_user' =>$loggedInUser
            ));
    }

    /**
     * @Route("useraccount/add_useraccount", name="useraccount_add_user")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addUserAccountAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $new_user = new UserAccount();

        $form = $this->createForm(new UserAccountType($loggedInUser), $new_user);
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
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("useraccount/add_useraccount_by_contact/{id}", name="useraccount_add_by_contact", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addUserAccountByContactAction($id, Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserContactRepository $userContactRepository */
        $userContactRepository = $em->getRepository('AppBundle\Entity\UserContact');

        /** @var UserContact $user_contact */
        $user_contact =$userContactRepository->find($id);

        $new_user = new UserAccount();

        $form = $this->createForm(new UserAccountType($loggedInUser), $new_user);
        $form->handleRequest($request);

        if ($form->isValid())
        {

            $new_user->setPassword($user_contact->getPassword());

            $em->persist($new_user);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('useraccount_add_by_contact', array('id' => $id));
        }

        return $this->render('users/addUserAccountByContact.html.twig',
            array(
                'new_user' => $new_user,
                'form' => $form->createView(),
                'user_contact' => $user_contact,
                'logged_in_user' => $loggedInUser
            ));
    }


    /**
     * Opens edit page for user account with passed $id.
     *
     * @Route("/useraccount/{id}", name="useraccount_edit_user", defaults={"id" = -1})
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function editUserAccountAction($id, Request $request) {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();
        if ($loggedInUser->getId() != $id && !$loggedInUser->getIsAdmin())
        {
            return $this->redirectToRoute('useraccount_edit_user', array(
                'id' => $loggedInUser->getId()
            ));
        }
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');
        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        /** @var PasswordUpdater $passwordHasher */
        $passwordHasher = $this->get('fos_user.util.password_updater');

        // Editing user account
        /** @var UserAccount $useraccount */
        $useraccount = $userAccountRepository->find($id);

        if (!$useraccount) {
            $this->addFlash(
                'error',
                'No user found with id: ' . $id . '!'
            );
            return $this->redirectToRoute('useraccount_list_all');
        }

        $form = $this->createForm(new UserAccountType($loggedInUser), $useraccount);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // DELETE user account
            if ($form->has('delete') && $form->get('delete')->isClicked()) {
                $em->remove($useraccount);
                $em->flush();

                // message
                $this->addFlash(
                    'notice',
                    'User account with id: ' . $id . ' has been deleted successfully!'
                );

                // show list
                return $this->redirectToRoute('useraccount_list_all');
            }
            // CHANGE password
            if($form->has('change_password') && $form->get('change_password')->isClicked()) {
                return $this->redirectToRoute('fos_user_change_password');
            }
            $em->persist($useraccount);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('useraccount_list_all');
        }

        return $this->render('users/editUserAccount.html.twig',
            array(
                'useraccount' => $useraccount,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("useraccount/useraccount_view/{id}", name="useraccount_view", defaults={"id" = -1})
     *
     * @param Request request
     * @return array
     */
    public function viewUserAccountAction($id, Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();
        if ($loggedInUser->getId() != $id && !$loggedInUser->getIsAdmin())
        {
            return $this->redirectToRoute('useraccount_view', array(
                'id' => $loggedInUser->getId()
            ));
        }
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $userAccount =$userAccountRepository->find($id);

        return $this->render('users/viewUserAccount.html.twig',
            array(
                'user_account' => $userAccount,
                'logged_in_user' => $loggedInUser
            ));
    }
}