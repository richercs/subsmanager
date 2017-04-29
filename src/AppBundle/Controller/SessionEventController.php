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
use AppBundle\Entity\UserAccount;
use AppBundle\Form\SessionEventType;
use AppBundle\Repository\ScheduleItemRepository;
use AppBundle\Repository\SessionEventRepository;
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
                $events = $sessionEventRepository->getLastFiftySessions();
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
                $events = $sessionEventRepository->getLastFiftySessionsFilteredScheduleItem($filteredScheduleItem);
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

        /** @var ScheduleItem $scheduleItem */
        foreach ($scheduleItemCollection as $key => $scheduleItem) {
            if($scheduleItem->isDeleted()) {
                unset($scheduleItemCollection[$key]);
            }
        }

        $newEvent = new SessionEvent();

        $form = $this->createForm(new SessionEventType($scheduleItemCollection), $newEvent);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            // save and continue button that redirects to the edit page
            if($form->get('saveAndContinue')->isClicked()) {
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
            // Check attendee records for errors
            // Rule #1 - Every attendance record is unique

            // TODO: Implement Rule #1
            // TODO: Is it possible to set it for form.row.error?

            // Rule #2 - Monthly subscription can only be used by the owner

            /** @var AttendanceHistory $attendee */
            foreach ($sessionEvent->getAttendees() as $attendee) {
                if ($attendee->getSubscription()->isIsMonthlyTicket() &&
                    $attendee->getAttendee() != $attendee->getSubscription()->getOwner())
                {
                    $this->addFlash(
                        'error',
                        'Hibás résztvevő: Havi bérletet csak a tulajdonos használhat!'
                    );
                    return $this->redirectToRoute('session_edit_session_event', array(
                        'id' => $id
                    ));
                }
            }

            // Rule #3 - Attendance count limit not reached by multiple usage

            // TODO: Implement Rule #3


            $em->persist($sessionEvent);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
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



}