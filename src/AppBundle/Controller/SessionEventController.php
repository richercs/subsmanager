<?php
/**
 * Created by PhpStorm.
 * User: csabi
 * Date: 1/27/17
 * Time: 6:42 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\SessionEvent;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\SessionEventType;
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

        /** @var UserAccountRepository $user_repo */
        $sessionEvent_repo = $em->getRepository('AppBundle\Entity\SessionEvent');

        $events = $sessionEvent_repo->findAll();

        return $this->render('event/listAllSessionEvents.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'events' => $events,
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

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $new_event = new SessionEvent();

        $form = $this->createForm(new SessionEventType(), $new_event);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            // The OneToMany association and the symfony-collection bundle manages the attendees ArrayCollection
            $em->persist($new_event);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('session_event_list_all');
        }

        return $this->render('event/addSessionEvent.html.twig',
            array(
                'new_event' => $new_event,
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
     * @param Request $request
     * @return array
     */
    public function editSessionEventAction($id, Request $request) {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SessionEventRepository $sessionEventRepository */
        $sessionEventRepository = $em->getRepository('AppBundle\Entity\SessionEvent');

        // Editing session event
        /** @var SessionEvent $sessionevent */
        $sessionevent = $sessionEventRepository->find($id);

        if (!$sessionevent) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú óra esemény: ' . $id . '!'
            );
            return $this->redirectToRoute('session_event_list_all');
        }

        $originalAttendees = new ArrayCollection();

        // Create an ArrayCollection of the current Attendance objects in the database
        foreach ($sessionevent->getAttendees() as $attendee) {
            $originalAttendees->add($attendee);
        }

        $form = $this->createForm(new SessionEventType(), $sessionevent);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // remove the relationship between the attendee and the Session event
            foreach ($originalAttendees as $attendee) {
                if (false === $sessionevent->getAttendees()->contains($attendee)) {
                    // remove the Session event from the Attendee
                    // $attendee->setSessionEvent(null);
                    // $em->persist($attendee);

                    // to delete the attendee entirely
                    $em->remove($attendee);
                }
            }
            // DELETE Session event
            if ($form->get('delete')->isClicked()) {
                $em->remove($sessionevent);
                $em->flush();

                // message
                $this->addFlash(
                    'notice',
                    $id . ' azonosítójú óra esemény sikeresen törölve!'
                );

                // show list
                return $this->redirectToRoute('session_event_list_all');
            }
            $em->persist($sessionevent);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('session_event_list_all');
        }

        return $this->render('event/editSessionEvent.html.twig',
            array(
                'sessionevent' => $sessionevent,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }

}