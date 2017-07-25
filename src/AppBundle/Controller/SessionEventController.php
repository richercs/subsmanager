<?php
/**
 * Created by PhpStorm.
 * User: csabi
 * Date: 1/27/17
 * Time: 6:42 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\SessionEvent;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\SessionEventType;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\ScheduleItemRepository;
use AppBundle\Repository\SessionEventRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SessionEventController extends Controller
{
    /**
     * @Route("/sessionevent/list_all", name="session_event_list_all")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function listAllSessionEventsAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SessionEventRepository $sessionEventRepo */
        $sessionEventRepo = $em->getRepository('AppBundle\Entity\SessionEvent');

        $events = $sessionEventRepo->findAll();

        return $this->render('event/listAllSessionEvents.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'events' => $events,
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("sessionevent/search_edit_sessionevent", name="sessionevent_search_edit")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function searchSessionEventsForEditAction(Request $request)
    {
        /** UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SessionEventRepository $sessionEventRepository */
        $sessionEventRepository = $em->getRepository('AppBundle\Entity\SessionEvent');

        $searchStartDate = $request->get('searchStart');

        $searchDueDate = $request->get('searchDue');

        $searchScheduleItemId = $request->get('searchScheduleItemId');

        if(is_null($searchScheduleItemId) || $searchScheduleItemId == "") {

            if(is_null($searchStartDate) && is_null($searchDueDate) || $searchStartDate == "" && $searchDueDate == "") {
                $events = $sessionEventRepository->getLastThirtySessions();
            } else {
                $events = $sessionEventRepository->getSessionsBetweenDates($searchStartDate, $searchDueDate);
            }
        } else {

            /** @var ScheduleItemRepository $scheduleItemRepository */
            $scheduleItemRepository = $em->getRepository(ScheduleItem::class);

            $filteredScheduleItem = $scheduleItemRepository->find($searchScheduleItemId);

            if (!$filteredScheduleItem) {
                $this->addFlash(
                    'error',
                    'Nincs ilyen azonosítójú órarendi elem: ' . $filteredScheduleItem . '!'
                );
            }

            if(is_null($searchStartDate) && is_null($searchDueDate) || $searchStartDate == "" && $searchDueDate == "") {
                $events = $sessionEventRepository->getLastThirtySessionsFilteredScheduleItem($filteredScheduleItem);
            } else {
                $events = $sessionEventRepository->getSessionsBetweenDatesFilteredScheduleItem($searchStartDate, $searchDueDate, $filteredScheduleItem);
            }
        }

        /** @var AdminStatsController $adminStatsController */
        $adminStatsController = $this->get('admin_stats');


        /** @var SessionEvent $event */
        foreach ($events as $event) {

            $revenue = $adminStatsController->calculateRevenueAction($event);

            $event->setRevenue($revenue);
        }

        return $this->render('event/listSessionEventsEdit.html.twig', array(
            'events' => $events,
            'searchStart' => $searchStartDate,
            'searchDue' =>$searchDueDate,
            'searchScheduleItemId' => $searchScheduleItemId,
            'logged_in_user' => $loggedInUser
        ));
    }

    /**
     * @Route("/sessionevent/add_session_event", name="session_add_session_event")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addSessionEventAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var ScheduleItemRepository $scheduleItemRepository */
        $scheduleItemRepository = $em->getRepository(ScheduleItem::class);

        $scheduleItemCollection = $scheduleItemRepository->findAll();

        $scheduleItemCollection = array_combine(range(1, count($scheduleItemCollection)), array_values($scheduleItemCollection));

        /** @var ScheduleItem $scheduleItem */
        foreach ($scheduleItemCollection as $key => $scheduleItem) {
            if($scheduleItem->isDeleted()) {
                unset($scheduleItemCollection[$key]);
            }
        }

        $newEvent = new SessionEvent();

        $form = $this->createForm(new SessionEventType($scheduleItemCollection, true), $newEvent);
        $form->handleRequest($request);

        if ($form->isValid())
        {

            $scheduleItemId = $request->get('appbundle_sessionevent')['scheduleItem'];

            $scheduleItem = $scheduleItemRepository->find($scheduleItemId);

            $newEvent->setScheduleItem($scheduleItem);

            // save and continue button that redirects to the edit page
            if($form->get('saveAndContinue')->isClicked()) {

                // Attendance Records Validation
                // Rule #1 - Every attendee is unique

                $duplicateAttendees = $this->validateAttendeesCount($newEvent);

                if (!empty($duplicateAttendees)) {
                    // message
                    $this->addFlash(
                        'error',
                        'Név többször szerpel az űrlapon: ' . PHP_EOL . implode(', ', $duplicateAttendees)
                    );

                    // show list
                    return $this->redirectToRoute('session_add_session_event');

                }

                // Rule #2 - One subscription can only be present on a session event twice

                $duplicateSubscriptions = $this->validateSubscriptionsCount($newEvent);

                if (!empty($duplicateSubscriptions)) {
                    // message
                    $this->addFlash(
                        'error',
                        'Bérlet többször szerpel az űrlapon mint 2, azonosító: ' . PHP_EOL . implode(', ', $duplicateSubscriptions)
                    );

                    // show list
                    return $this->redirectToRoute('session_add_session_event');
                }

                // The OneToMany association and the symfony-collection bundle manages the attendees ArrayCollection
                $em->persist($newEvent);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Változtatások Elmentve!'
                );
                return $this->redirectToRoute('session_edit_session_event', array(
                    'id' => $newEvent->getId()
                ));
            }

            // Attendance Records Validation
            // Rule #1 - Every attendee is unique

            $duplicateAttendees = $this->validateAttendeesCount($newEvent);

            if (!empty($duplicateAttendees)) {
                // message
                $this->addFlash(
                    'error',
                    'Név többször szerpel az űrlapon: ' . PHP_EOL . implode(', ', $duplicateAttendees)
                );

                // show list
                return $this->redirectToRoute('session_add_session_event');

            }

            // Rule #2 - One subscription can only be present on a session event twice

            $duplicateSubscriptions = $this->validateSubscriptionsCount($newEvent);

            if (!empty($duplicateSubscriptions)) {
                // message
                $this->addFlash(
                    'error',
                    'Bérlet többször szerpel az űrlapon mint 2, azonosító: ' . PHP_EOL . implode(', ', $duplicateSubscriptions)
                );

                // show list
                return $this->redirectToRoute('session_add_session_event');
            }

            // The OneToMany association and the symfony-collection bundle manages the attendees ArrayCollection
            $em->persist($newEvent);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('sessionevent_search_edit');
        }

        return $this->render('event/addSessionEvent.html.twig',
            array(
                'new_event' => $newEvent,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));

    }

    /**
     * Opens edit page for session events with passed $id.
     *
     * @Route("/sessionevent/{id}", name="session_edit_session_event", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param $subscriptionId
     * @param Request $request
     * @return array
     */
    public function editSessionEventAction($id, $subscriptionId = null, Request $request) {

        // This is for the back URL
        if (!is_null($request->query->get('subscription_id'))) {
            $subscriptionId = intval($request->query->get('subscription_id'));
        }

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SessionEventRepository $sessionEventRepository */
        $sessionEventRepository = $em->getRepository('AppBundle\Entity\SessionEvent');

        // This is for the back URL
        $searchStartDate = $request->get('searchStart');

        $searchDueDate = $request->get('searchDue');

        $searchScheduleItemId = $request->get('searchScheduleItemId');

        // Editing session event
        /** @var SessionEvent $sessionEvent */
        $sessionEvent = $sessionEventRepository->find($id);

        if (!$sessionEvent) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú óra esemény: ' . $id . '!'
            );
            return $this->redirectToRoute('sessionevent_search_edit');
        }

        /** @var AttendanceHistory $attendee */
        foreach ($sessionEvent->getAttendees() as $attendee) {

            if(!is_null($attendee->getSubscription())) {

                $subscriptionIdInRecord = $attendee->getSubscription()->getId();

                /** @var SubscriptionRepository $repository */
                $repository = $this->get('doctrine.orm.default_entity_manager')->getRepository(Subscription::class);

                /** @var Subscription $subscription */
                $subscription = $repository->find($subscriptionIdInRecord);

                /** @var AttendanceHistoryRepository $attendaceHistoryRepo */
                $attendaceHistoryRepo = $this->get('doctrine.orm.default_entity_manager')->getRepository(AttendanceHistory::class);

                $subscriptionUsages = $attendaceHistoryRepo->findBy(array('subscription' => $subscriptionIdInRecord));

                $countOfSubscriptionUsages = count($subscriptionUsages);

                if(is_null($subscription->getAttendanceCount())) {
                    $attendee->setSubscriptionInfo("Havi bérlet (használatok száma: " . $countOfSubscriptionUsages . ")");
                } else {
                    $attendee->setSubscriptionInfo("Alkalmak Száma: " . $subscription->getAttendanceCount()
                        . "\n"
                        . "Fennmaradó: " . ($subscription->getAttendanceCount() - $countOfSubscriptionUsages));
                }
            }
        }

        $originalAttendees = new ArrayCollection();

        // Create an ArrayCollection of the current Attendance objects in the database
        foreach ($sessionEvent->getAttendees() as $attendee) {
            $originalAttendees->add($attendee);
        }


        $form = $this->createForm(new SessionEventType(), $sessionEvent);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // remove the relationship between the attendee and the Session event
            foreach ($originalAttendees as $attendee) {
                if (false === $sessionEvent->getAttendees()->contains($attendee)) {
                    // remove the Session event from the Attendee
                    // $attendee->setSessionEvent(null);
                    // $em->persist($attendee);

                    // to delete the attendee entirely
                    $em->remove($attendee);
                }
            }
            // DELETE Session event
            if ($form->get('delete')->isClicked()) {
                $em->remove($sessionEvent);
                $em->flush();

                // message
                $this->addFlash(
                    'notice',
                    '"'. $id . '" azonosítójú óra esemény sikeresen törölve!'
                );

                // show list
                return $this->redirectToRoute('sessionevent_search_edit', array(
                    'searchStart' => $searchStartDate,
                    'searchDue' =>$searchDueDate,
                    'searchScheduleItemId' => $searchScheduleItemId,
                ));
            }

            // Attendance Records Validation
            // Rule #1 - Every attendee is unique

            $duplicateAttendees = $this->validateAttendeesCount($sessionEvent);

            if (!empty($duplicateAttendees)) {
                // message
                $this->addFlash(
                    'error',
                    'Név többször szerpel az űrlapon: ' . PHP_EOL . implode(', ', $duplicateAttendees)
                );

                // show list
                return $this->redirectToRoute('session_edit_session_event', array(
                    'id' => $sessionEvent->getId()
                ));

            }

            // Rule #2 - One subscription can only be present on a session event twice

            $duplicateSubscriptions = $this->validateSubscriptionsCount($sessionEvent);

            if (!empty($duplicateSubscriptions)) {
                // message
                $this->addFlash(
                    'error',
                    'Bérlet többször szerpel az űrlapon mint 2, azonosító: ' . PHP_EOL . implode(', ', $duplicateSubscriptions)
                );

                // show list
                return $this->redirectToRoute('session_edit_session_event', array(
                    'id' => $sessionEvent->getId()
                ));
            }



            $em->persist($sessionEvent);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );


            // Update the subscription Info textareas on each Attendance Record

            /** @var SessionEvent $sessionEvent */
            $sessionEvent = $sessionEventRepository->find($id);

            /** @var AttendanceHistory $attendee */
            foreach ($sessionEvent->getAttendees() as $attendee) {

                if(!is_null($attendee->getSubscription())) {

                    $subscriptionIdInRecord = $attendee->getSubscription()->getId();

                    /** @var SubscriptionRepository $repository */
                    $repository = $this->get('doctrine.orm.default_entity_manager')->getRepository(Subscription::class);

                    /** @var Subscription $subscription */
                    $subscription = $repository->find($subscriptionIdInRecord);

                    /** @var AttendanceHistoryRepository $attendaceHistoryRepo */
                    $attendaceHistoryRepo = $this->get('doctrine.orm.default_entity_manager')->getRepository(AttendanceHistory::class);

                    $subscriptionUsages = $attendaceHistoryRepo->findBy(array('subscription' => $subscriptionIdInRecord));

                    $countOfSubscriptionUsages = count($subscriptionUsages);

                    if(is_null($subscription->getAttendanceCount())) {
                        $attendee->setSubscriptionInfo("Havi bérlet (használatok száma: " . $countOfSubscriptionUsages . ")");
                    } else {
                        $attendee->setSubscriptionInfo("Alkalmak Száma: " . $subscription->getAttendanceCount()
                            . "\n"
                            . "Fennmaradó: " . ($subscription->getAttendanceCount() - $countOfSubscriptionUsages));
                    }
                }
            }

            // TODO: Validate this code pls. Is it ok to recreate the form and what happens at next submit?

            $form = $this->createForm(new SessionEventType(), $sessionEvent);

            return $this->render('event/editSessionEvent.html.twig',
                array(
                    'sessionevent' => $sessionEvent,
                    'searchStart' => $searchStartDate,
                    'searchDue' =>$searchDueDate,
                    'searchScheduleItemId' => $searchScheduleItemId,
                    'form' => $form->createView(),
                    'subscription_id' => $subscriptionId,
                    'logged_in_user' => $loggedInUser
                ));
        }

        return $this->render('event/editSessionEvent.html.twig',
            array(
                'sessionevent' => $sessionEvent,
                'searchStart' => $searchStartDate,
                'searchDue' =>$searchDueDate,
                'searchScheduleItemId' => $searchScheduleItemId,
                'form' => $form->createView(),
                'subscription_id' => $subscriptionId,
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     *
     * @Route("/sessionevent/view_sessionevent/{id}", name="sessionevent_view", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function viewSessionEvent($id, Request $request) {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SessionEventRepository $sessionEventRepo */
        $sessionEventRepo = $em->getRepository('AppBundle\Entity\SessionEvent');

        /** @var SessionEvent $sessionevent */
        $sessionevent = $sessionEventRepo->find($id);

        if (!$sessionevent) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú óra esemény: ' . $id . '!'
            );
            return $this->redirectToRoute('sessionevent_search_edit');
        }

        /** @var AdminStatsController $adminStatsController */
        $adminStatsController = $this->get('admin_stats');

        $revenue = $adminStatsController->calculateRevenueAction($sessionevent);

        $sessionevent->setRevenue($revenue);

        return $this->render('event/viewSessionEvent.html.twig',
            array(
                'sessionevent' => $sessionevent,
                'logged_in_user' => $loggedInUser
            ));


    }

    /**
     *
     * @param SessionEvent $sessionEvent
     *
     * $return array
     */
    public function validateAttendeesCount($sessionEvent) {

        // Attendee validation
        // Rule #1 - Every attendee is unique

        $attendees = new ArrayCollection();

        /** @var AttendanceHistory $attendee */
        foreach ($sessionEvent->getAttendees() as $attendee) {
            $attendees->add($attendee->getAttendee());
        }

        $duplicates = new ArrayCollection();

        /** @var UserAccount $attendee */
        foreach ($attendees as $attendee) {

            $countDuplicates = 0;

            foreach ($attendees as $attendeetoCheck) {
                if ($attendee == $attendeetoCheck) {
                    $countDuplicates = $countDuplicates + 1;
                }
            }

            if ($countDuplicates >= 2) {

                $duplicates->add($attendee->getUsername());
            }
        }

        if ($duplicates->count() >= 1) {
            return $duplicates->toArray();
        }
        return array();
    }

    /**
     *
     * @param SessionEvent $sessionEvent
     *
     * $return array
     */
    public function validateSubscriptionsCount($sessionEvent) {

        // Subscription validation
        // Rule #2 - One subscription can only be present on a session event twice

        $subscriptions = new ArrayCollection();

        /** @var AttendanceHistory $attendanceRecord */
        foreach ($sessionEvent->getAttendees() as $attendanceRecord) {
            $subscriptions->add($attendanceRecord->getSubscription());
        }

        $duplicates = new ArrayCollection();

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {

            $countDuplicates = 0;

            foreach ($subscriptions as $subscriptiontoCheck) {
                if ($subscription == $subscriptiontoCheck) {
                    $countDuplicates = $countDuplicates + 1;
                }
            }

            if ($countDuplicates >= 3) {

                $duplicates->add($subscription->getId());
            }
        }

        if ($duplicates->count() >= 1) {
            return $duplicates->toArray();
        }
        return array();
    }

}