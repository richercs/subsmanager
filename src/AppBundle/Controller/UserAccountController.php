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

        /** @var UserAccountRepository $userRepo */
        $userRepo = $em->getRepository('AppBundle\Entity\UserAccount');

        $users = $userRepo->findAll();

        return $this->render('users/listAllUserAccounts.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'users' => $users,
                'logged_in_user' => $loggedInUser
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

        $newUserAccount = new UserAccount();

        $form = $this->createForm(new UserAccountType($loggedInUser), $newUserAccount);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($newUserAccount);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('useraccount_add_user');
        }

        return $this->render('users/addUserAccount.html.twig',
            array(
                'new_user' => $newUserAccount,
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
        $userContact = $userContactRepository->find($id);

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $userCollection = $userAccountRepository->getAllWithNullEmail();

        $newUserAccount = new UserAccount();

        $form = $this->createForm(new UserAccountType($loggedInUser, $userCollection, true), $newUserAccount);
        $form->handleRequest($request);

        $sourceUserId = $request->get('appbundle_useraccount')['source_user_account_id'];

        if ($form->isValid()) {

            if (empty($sourceUserId)) {
                $newUserAccount->setPassword($userContact->getPassword());
                $newUserAccount->setEnabled(true);
                $em->persist($newUserAccount);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Változtatások Elmentve!'
                );

            } else {
                /** @var UserAccount $disabledUser */
                $disabledUser = $userAccountRepository->find($sourceUserId);
                if ($disabledUser->getPassword() == "not_set") {
                    $disabledUser->setPassword($userContact->getPassword());
                    $disabledUser->setEnabled(true);
                }

                $disabledUser->setFirstName($newUserAccount->getFirstName());

                $disabledUser->setLastName($newUserAccount->getLastName());

                $disabledUser->setEmail($newUserAccount->getEmail());

                $disabledUser->setUsername($newUserAccount->getUsername());

                $em->persist($disabledUser);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Változtatások Elmentve!'
                );
            }
            return $this->redirectToRoute('useraccount_add_by_contact', array('id' => $id));
        }

        return $this->render('users/addUserAccountByContact.html.twig',
            array(
                'new_user' => $newUserAccount,
                'form' => $form->createView(),
                'user_contact' => $userContact,
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
    public function editUserAccountAction($id, Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();
        if ($loggedInUser->getId() != $id && !$loggedInUser->getIsAdmin()) {
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
        /** @var UserAccount $userAccount */
        $userAccount = $userAccountRepository->find($id);

        if (!$userAccount) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú felhasználó: ' . $id . '!'
            );
            return $this->redirectToRoute('useraccount_list_all');
        }

        $form = $this->createForm(new UserAccountType($loggedInUser), $userAccount);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // DELETE user account
            if ($form->has('delete') && $form->get('delete')->isClicked()) {
                $userAccount->setEnabled(false);
                $em->persist($userAccount);
                $em->flush(); // with just the remove/flush the previous changes wouldn't be updated
                $em->remove($userAccount);
                $em->flush();

                // message
                $this->addFlash(
                    'notice',
                    '"' . $id . '" azonosítójú felhasználó sikeresen törölve!'
                );

                // show list
                return $this->redirectToRoute('useraccount_list_all');
            }
            // CHANGE password
            if ($form->has('change_password') && $form->get('change_password')->isClicked()) {
                // check if the user id is the same as the id in the request
                if ($loggedInUser->getId() == $id) {
                    return $this->redirectToRoute('fos_user_change_password');
                } else {
                    $this->addFlash(
                        'error',
                        'A felhasználó jelszava nem változtatható meg, azonosító: ' . $id . '!'
                    );
                    return $this->redirectToRoute('useraccount_edit_user', array(
                        'id' => $loggedInUser->getId()
                    ));
                }
            }
            $em->persist($userAccount);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('useraccount_list_all');
        }

        return $this->render('users/editUserAccount.html.twig',
            array(
                'useraccount' => $userAccount,
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
        if ($loggedInUser->getId() != $id && !$loggedInUser->getIsAdmin()) {
            return $this->redirectToRoute('useraccount_view', array(
                'id' => $loggedInUser->getId()
            ));
        }
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $userAccount = $userAccountRepository->find($id);

        return $this->render('users/viewUserAccount.html.twig',
            array(
                'user_account' => $userAccount,
                'logged_in_user' => $loggedInUser
            ));
    }
}