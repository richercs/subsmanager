<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\SessionSignUp;
use AppBundle\Form\AnnouncedSessionType;
use AppBundle\Repository\AnnouncedSessionRepository;
use AppBundle\Repository\ScheduleItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;


class AnnouncedSessionController extends Controller
{
    /**
     * @Route("announced_session/search_edit_announced_session", name="announced_session_search_edit")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function searchAnnouncedSessionForEditAction(Request $request)
    {
        /** UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var AnnouncedSessionRepository $announcedSessionRepository */
        $announcedSessionRepository = $em->getRepository('AppBundle\Entity\AnnouncedSession');

        $searchStartDate = $request->get('searchStart');

        $searchDueDate = $request->get('searchDue');

        $searchScheduleItemId = $request->get('searchScheduleItemId');

        if(is_null($searchScheduleItemId) || $searchScheduleItemId == "") {

            if(is_null($searchStartDate) && is_null($searchDueDate) || $searchStartDate == "" && $searchDueDate == "") {
                $announcedSessions = $announcedSessionRepository->getLastThirty();
            } else {
                $announcedSessions = $announcedSessionRepository->getBetweenDates($searchStartDate, $searchDueDate);
            }
        } else {
            /** @var ScheduleItemRepository $scheduleItemRepository */
            $scheduleItemRepository = $em->getRepository('AppBundle\Entity\ScheduleItem');

            $filteredScheduleItem = $scheduleItemRepository->find($searchScheduleItemId);

            if (!$filteredScheduleItem) {
                $this->addFlash(
                    'error',
                    'Nincs ilyen azonosítójú órarendi elem: ' . $filteredScheduleItem . '!'
                );
            }
            if(is_null($searchStartDate) && is_null($searchDueDate) || $searchStartDate == "" && $searchDueDate == "") {
                $announcedSessions = $announcedSessionRepository->getLastThirtyFilteredScheduleItem($filteredScheduleItem);
            } else {
                $announcedSessions = $announcedSessionRepository->getBetweenDatesFilteredScheduleItem($searchStartDate, $searchDueDate, $filteredScheduleItem);
            }
        }

        // Set extra info needed to list entities
        /** @var AnnouncedSession $announcedSession */
        foreach ($announcedSessions as $announcedSession) {

            // TODO: implement function
            $numberOfSignees =  $this->calculateNumberOfSignees($announcedSession);

            $announcedSession->setNumberOfSignees($numberOfSignees);
        }

        // Get every schedule item for rendering the select on search form
        /** @var ScheduleItemRepository $scheduleItemRepository */
        $scheduleItemRepository = $em->getRepository('AppBundle\Entity\ScheduleItem');

        $scheduleItems = $scheduleItemRepository->findAll();

        return $this->render('signups\listAnnouncedSessionsEdit.html.twig', array(
            'announcedSessions' => $announcedSessions,
            'searchStart' => $searchStartDate,
            'searchDue' =>$searchDueDate,
            'searchScheduleItemId' => $searchScheduleItemId,
            'schedule_items' => $scheduleItems,
            'logged_in_user' => $loggedInUser
        ));

    }

    /**
     * @Route("/announced_session/add_announced_session", name="add_announced_session")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addAnnouncedSessionAction(Request $request)
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

        $newAnnouncedSession = new AnnouncedSession();

        $form = $this->createForm(new AnnouncedSessionType($scheduleItemCollection, true), $newAnnouncedSession);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $scheduleItemId = $request->get('appbundle_announced_session')['scheduleItem'];

            $scheduleItem = $scheduleItemRepository->find($scheduleItemId);

            $newAnnouncedSession->setScheduleItem($scheduleItem);


            // TODO: Validate by business rules if need be

            // The OneToMany association and the symfony-collection bundle manages the signups ArrayCollection
            $em->persist($newAnnouncedSession);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );


            return $this->redirectToRoute('add_announced_session');
        }

        return $this->render('signups/addAnnouncedSession.html.twig',
            array(
                'new_announced_session' => $newAnnouncedSession,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("/announced_session/{id}", name="edit_announced_session", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function editAnnouncedSessionAction($id, Request $request)
    {

    }

    /**
     * Calculates the number of signees of one announced session.
     *
     * @param AnnouncedSession $announcedSession
     * @return int
     */
    private function calculateNumberOfSignees(AnnouncedSession $announcedSession)
    {
        return 0;
    }
}