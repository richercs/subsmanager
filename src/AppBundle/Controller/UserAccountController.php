<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserContact;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserContactRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @Route("/useraccount/search_useraccount", name="useraccount_search_useraccount")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function searchUserAccountsAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userRepo */
        $userRepo = $em->getRepository('AppBundle\Entity\UserAccount');

        $searchLikeUserName = $request->get('searchLikeUsername');

        $users = $userRepo->findLikeUserName($searchLikeUserName);

        return $this->render('users/searchUserAccounts.html.twig',
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

        if(is_null($loggedInUser)) {
            return $this->redirectToRoute('fos_user_security_login');
        }

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
            return $this->redirectToRoute('useraccount_search_useraccount');
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
                return $this->redirectToRoute('useraccount_search_useraccount');
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

                    if($loggedInUser->getIsAdmin()) {
                        return $this->redirectToRoute('useraccount_edit_user', array(
                            'id' => $id
                        ));
                    }
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

            if($loggedInUser->getIsAdmin()) {
                return $this->redirectToRoute('useraccount_edit_user', array(
                    'id' => $id
                ));
            }
            return $this->redirectToRoute('useraccount_edit_user', array(
                'id' => $loggedInUser->getId()
            ));
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

        if(is_null($loggedInUser)) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        if ($loggedInUser->getId() != $id && !$loggedInUser->getIsAdmin()) {
            return $this->redirectToRoute('useraccount_view', array(
                'id' => $loggedInUser->getId()
            ));
        }
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        /** @var UserAccount $userAccount */
        $userAccount = $userAccountRepository->find($id);

        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $em->getRepository('AppBundle\Entity\Subscription');

        $subscriptions = $subscriptionRepository->findBy(array('owner' => $userAccount->getId()));

        // Show only active subscriptions
        // Two conditions for active subscriptions:

        /** @var AttendanceHistoryRepository $attendanceHistoryRepo */
        $attendanceHistoryRepo = $em->getRepository(AttendanceHistory::class);

        /** ArrayCollection $activeSubs */
        $activeSubs = new ArrayCollection();

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {

            // Condition #1 - the due date of the subscription is not in the past
            if($subscription->getStatusBoolean()) {

                //Condition #2 - actual usage count is lower then maximum usage count allowed
                $usageCount = count($attendanceHistoryRepo->findBy(array('subscription' => $subscription)));

                $subscription->setUsages($usageCount);

                if($subscription->getUsages() < $subscription->getAttendanceCount()) {

                    $activeSubs->add($subscription);
                }
            }
        }

        return $this->render('users/viewUserAccount.html.twig',
            array(
                'user_account' => $userAccount,
                'logged_in_user' => $loggedInUser,
                'subscriptions' => $activeSubs
            ));
    }

    /**
     * @Route("useraccount/useraccount_view_all_subs/{id}", name="useraccount_view_all_subs", defaults={"id" = -1})
     *
     * @param Request request
     * @return array
     */
    public function viewUserAccountAllSubsAction($id, Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        if(is_null($loggedInUser)) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        if ($loggedInUser->getId() != $id && !$loggedInUser->getIsAdmin()) {
            return $this->redirectToRoute('useraccount_view', array(
                'id' => $loggedInUser->getId()
            ));
        }
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        /** @var UserAccount $userAccount */
        $userAccount = $userAccountRepository->find($id);

        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $em->getRepository('AppBundle\Entity\Subscription');

        $subscriptions = $subscriptionRepository->findBy(array('owner' => $userAccount->getId()));

        return $this->render('users/viewUserAccountAllSubs.html.twig',
            array(
                'user_account' => $userAccount,
                'logged_in_user' => $loggedInUser,
                'subscriptions' => $subscriptions
            ));
    }
}