<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\SubscriptionType;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\SessionEventRepository;
use AppBundle\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SubscriptionController extends Controller
{
    /**
     * @Route("/subscription/list_all", name="subscription_list_all")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function listAllSubscriptionsAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SubscriptionRepository $subscriptionRepo */
        $subscriptionRepo = $em->getRepository('AppBundle\Entity\Subscription');

        $subscriptions = $subscriptionRepo->findAll();

        return $this->render('subscription/listAllSubscriptions.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'subscriptions' => $subscriptions,
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("/subscription/add_subscription", name="subscription_add_subscription")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addSubscriptionAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $newSubscription = new Subscription();

        $form = $this->createForm(new SubscriptionType(), $newSubscription);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($newSubscription);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('subscription_add_subscription');
        }

        return $this->render('subscription/addSubscription.html.twig',
            array(
                'new_subscription' => $newSubscription,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));

    }

    /**
     * Opens edit page for subscriptions with passed $id.
     *
     * @Route("/subscription/{id}", name="subscription_edit_subscription", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param $breakEventId
     * @param Request $request
     * @return array
     */
    public function editSubscriptionAction($id, $breakEventId = null, Request $request) {

        // This is for the back URL
        if (!is_null($request->query->get('break_event_id'))) {
            $breakEventId = intval($request->query->get('break_event_id'));
        }

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');
        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $em->getRepository('AppBundle\Entity\Subscription');

        // Editing subscription
        /** @var Subscription $subscription */
        $subscription = $subscriptionRepository->find($id);

        if (!$subscription) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú bérlet: ' . $id . '!'
            );
            return $this->redirectToRoute('subscription_list_all');
        }

        $form = $this->createForm(new SubscriptionType(), $subscription);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // DELETE subscription
            if ($form->get('delete')->isClicked()) {

                return $this->redirectToRoute('subscription_check_sessionsevents', array(
                    'id' => $subscription->getId()
                ));
            }
            $em->persist($subscription);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );

            $userAccountIDFromGet = $request->get('user_account_id');

            if(!is_null($userAccountIDFromGet)) {

                return $this->render('subscription/editSubscription.html.twig',
                    array(
                        'subscription' => $subscription,
                        'user_account_id' => intval($userAccountIDFromGet),
                        'form' => $form->createView(),
                        'break_event_id' => $breakEventId,
                        'logged_in_user' => $loggedInUser
                    ));
            }

            return $this->render('subscription/editSubscription.html.twig',
                array(
                    'subscription' => $subscription,
                    'form' => $form->createView(),
                    'break_event_id' => $breakEventId,
                    'logged_in_user' => $loggedInUser
                ));
        }

        $userAccountIDFromGet = $request->get('user_account_id');

        if(!is_null($userAccountIDFromGet)) {

            return $this->render('subscription/editSubscription.html.twig',
                array(
                    'subscription' => $subscription,
                    'user_account_id' => intval($userAccountIDFromGet),
                    'form' => $form->createView(),
                    'break_event_id' => $breakEventId,
                    'logged_in_user' => $loggedInUser
                ));
        }

        return $this->render('subscription/editSubscription.html.twig',
            array(
                'subscription' => $subscription,
                'form' => $form->createView(),
                'break_event_id' => $breakEventId,
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     *
     * @Route("/subscription/view_attendances/{id}", name="subscription_view_attendances", defaults={"id" = -1})
     *
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function viewSubscriptionAttendances($id, Request $request) {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SubscriptionRepository $subscriptionRepo */
        $subscriptionRepo = $em->getRepository('AppBundle\Entity\Subscription');

        /** @var Subscription $subscription */
        $subscription = $subscriptionRepo->find($id);

        if (!$subscription) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú bérlet: ' . $id . '!'
            );
            return $this->redirectToRoute('subscription_list_all');
        }

        /** @var AttendanceHistoryRepository $attendanceHistoryRepo */
        $attendanceHistoryRepo =$em->getRepository('AppBundle\Entity\AttendanceHistory');

        $attendancRecords = $attendanceHistoryRepo->findBy(array('subscription' => $subscription->getId()));


        return $this->render('subscription/viewSubscriptionAttendances.html.twig',
            array(
                'subscription' => $subscription,
                'attendances' => $attendancRecords,
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * Check if the subscription is in any ssession events
     *
     * @Route("/subscription/check_sessions/{id}", name="subscription_check_sessionsevents", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function checkSubscriptionInSessionEventsAction($id, Request $request) {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SubscriptionRepository $subscriptionRepo */
        $subscriptionRepo = $em->getRepository('AppBundle\Entity\Subscription');

        /** @var Subscription $subscription */
        $subscription = $subscriptionRepo->find($id);

        if (!$subscription) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú szünet esemény: ' . $id . '!'
            );
            return $this->redirectToRoute('subscription_list_all');
        }

        $relatedAttendanceRecords = $em->getRepository(AttendanceHistory::class)->findBy(array('subscription' => $id));

        if (!empty($relatedAttendanceRecords)) {
            // message
            $this->addFlash(
                'error',
                'A bérlet használatban van a következő űrlapokon: ' . PHP_EOL . implode(', ', $relatedAttendanceRecords)
            );

            $relatedAHSessionEvents = new ArrayCollection();

            /** @var AttendanceHistory $record */
            foreach ($relatedAttendanceRecords as $record) {
                $relatedAHSessionEvents->add($record->getSessionEvent());
            }

            return $this->render('event/listAllSessionEvents.html.twig', array(
                'events' => $relatedAHSessionEvents,
                'subscription_id' => $id,
                'logged_in_user' => $loggedInUser
            ));

        }

        // remove
        $em->remove($subscription);
        $em->flush();

        // message
        $this->addFlash(
            'notice',
            '"' . $id . '" azonosítójú bérlet sikeresen törölve!'
        );

        // show list
        return $this->redirectToRoute('subscription_list_all');
    }
}