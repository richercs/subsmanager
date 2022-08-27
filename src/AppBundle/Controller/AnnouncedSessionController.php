<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\AnnouncedSessionType;
use AppBundle\Repository\AnnouncedSessionRepository;
use AppBundle\Repository\ScheduleItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class AnnouncedSessionController extends Controller
{
	/**
	 * @Route("announced_session/search_edit_announced_session", name="announced_session_search_edit")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response|null
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

		if ($searchScheduleItemId === null || $searchScheduleItemId === "") {

			if (is_null($searchStartDate) && is_null($searchDueDate) || $searchStartDate == "" && $searchDueDate == "") {
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
			if (is_null($searchStartDate) && is_null($searchDueDate) || $searchStartDate == "" && $searchDueDate == "") {
				$announcedSessions = $announcedSessionRepository->getLastThirtyFilteredScheduleItem($filteredScheduleItem);
			} else {
				$announcedSessions = $announcedSessionRepository->getBetweenDatesFilteredScheduleItem($searchStartDate, $searchDueDate, $filteredScheduleItem);
			}
		}

		// Set extra info needed to list entities
		/** @var AnnouncedSession $announcedSession */
		foreach ($announcedSessions as $announcedSession) {
			$announcedSession->calculateNumberOfSignees();
		}

		// Get every schedule item for rendering the select on search form
		/** @var ScheduleItemRepository $scheduleItemRepository */
		$scheduleItemRepository = $em->getRepository('AppBundle\Entity\ScheduleItem');

		$scheduleItems = $scheduleItemRepository->findAll();

		return $this->render('signups\listAnnouncedSessionsEdit.html.twig',
			[
				'announcedSessions' => $announcedSessions,
				'searchStart' => $searchStartDate,
				'searchDue' => $searchDueDate,
				'searchScheduleItemId' => $searchScheduleItemId,
				'schedule_items' => $scheduleItems,
				'logged_in_user' => $loggedInUser
			]
		);
	}

	/**
	 * @Route("/announced_session/add_announced_session", name="add_announced_session")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response|RedirectResponse|null
	 * @throws OptimisticLockException
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
			if ($scheduleItem->isDeleted() || $scheduleItem->getIsWeeklyOnline()) {
				unset($scheduleItemCollection[$key]);
			}
		}

		$newAnnouncedSession = new AnnouncedSession();
		$newAnnouncedSession->setAnnouncedSessionType(AnnouncedSession::ANNOUNCED_SESSION_TYPE_SINGLE_LIMITED);

		$form = $this->createForm(new AnnouncedSessionType($newAnnouncedSession, $scheduleItemCollection, true), $newAnnouncedSession);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$scheduleItemId = $request->get('appbundle_announced_session')['scheduleItem'];

			$scheduleItem = $scheduleItemRepository->find($scheduleItemId);

			$newAnnouncedSession->setScheduleItem($scheduleItem);

			// The OneToMany association and the symfony-collection bundle manages the signups ArrayCollection
			$em->persist($newAnnouncedSession);
			$em->flush();
			$this->addFlash(
				'notice',
				'Változtatások Elmentve!'
			);

			if ($form->get('saveAndContinue')->isClicked()) {
				return $this->redirectToRoute('edit_announced_session', [
					'id' => $newAnnouncedSession->getId()
				]);
			}

			return $this->redirectToRoute('add_announced_session');
		}

		return $this->render('signups/addAnnouncedSession.html.twig',
			[
				'new_announced_session' => $newAnnouncedSession,
				'form' => $form->createView(),
				'logged_in_user' => $loggedInUser
			]
		);
	}

	/**
	 * @Route("/announced_session/add_weekly_online_announced_session", name="add_weekly_online_announced_session")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response|RedirectResponse|null
	 * @throws OptimisticLockException
	 */
	public function addWeeklyOnlineAnnouncedSessionAction(Request $request)
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
			if ($scheduleItem->isDeleted() || !$scheduleItem->getIsWeeklyOnline()) {
				unset($scheduleItemCollection[$key]);
			}
		}

		$newAnnouncedSession = new AnnouncedSession();
		$newAnnouncedSession->setAnnouncedSessionType(AnnouncedSession::ANNOUNCED_SESSION_TYPE_WEEKLY_ONLINE);

		$form = $this->createForm(new AnnouncedSessionType($newAnnouncedSession, $scheduleItemCollection, true), $newAnnouncedSession);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$scheduleItemId = $request->get('appbundle_announced_session')['scheduleItem'];

			$scheduleItem = $scheduleItemRepository->find($scheduleItemId);

			$newAnnouncedSession->setScheduleItem($scheduleItem);

			// The OneToMany association and the symfony-collection bundle manages the signups ArrayCollection
			$em->persist($newAnnouncedSession);
			$em->flush();
			$this->addFlash(
				'notice',
				'Változtatások Elmentve!'
			);

			if ($form->get('saveAndContinue')->isClicked()) {
				return $this->redirectToRoute('edit_announced_session', [
					'id' => $newAnnouncedSession->getId()
				]);
			}

			return $this->redirectToRoute('add_weekly_online_announced_session');
		}

		return $this->render('signups/addAnnouncedSession.html.twig',
			[
				'new_announced_session' => $newAnnouncedSession,
				'form' => $form->createView(),
				'logged_in_user' => $loggedInUser
			]
		);
	}

	/**
	 * @Route("/announced_session/{id}", name="edit_announced_session", defaults={"id" = -1})
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param $id
	 * @param Request $request
	 * @return RedirectResponse|Response|null
	 * @throws OptimisticLockException
	 */
	public function editAnnouncedSessionAction($id, Request $request)
	{
		/** @var UserAccount $loggedInUser */
		$loggedInUser = $this->getUser();

		/** @var EntityManager $em */
		$em = $this->get('doctrine.orm.default_entity_manager');

		/** @var AnnouncedSessionRepository $announcedSessionRepository */
		$announcedSessionRepository = $em->getRepository('AppBundle\Entity\AnnouncedSession');

		// This is for the back URL
		$searchStartDate = $request->get('searchStart');
		$searchDueDate = $request->get('searchDue');
		$searchScheduleItemId = $request->get('searchScheduleItemId');

		/** @var AnnouncedSession $announcedSession */
		$announcedSession = $announcedSessionRepository->find($id);

		if (!$announcedSession) {
			$this->addFlash(
				'error',
				'Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!'
			);
			return $this->redirectToRoute('announced_session_search_edit');
		}

		// this is for later on hard deletion from database instead of setting null parent id on child records
		$originalSignees = new ArrayCollection();

		foreach ($announcedSession->getSignees() as $signee) {
			$originalSignees->add($signee);
		}

		$form = $this->createForm(new AnnouncedSessionType($announcedSession), $announcedSession);
		$form->handleRequest($request);

		if ($form->isValid()) {
			// hard delete the sign-up records from database
			foreach ($originalSignees as $signee) {
				if (!$announcedSession->getSignees()->contains($signee)) {
					$em->remove($signee);
				}
			}
			if ($form->get('delete')->isClicked()) {

				if (!empty($announcedSession->getSessionEvent())) {
					$announcedSession->getSessionEvent()->setAnnouncedSession(null);
				}

				$em->remove($announcedSession);
				$em->flush();

				// message
				$this->addFlash(
					'notice',
					'"' . $id . '" azonosítójú bejelentkezéses óra sikeresen törölve!'
				);

				// show list
				return $this->redirectToRoute('announced_session_search_edit',
					[
						'searchStart' => $searchStartDate,
						'searchDue' => $searchDueDate,
						'searchScheduleItemId' => $searchScheduleItemId,
					]
				);
			}

			$em->persist($announcedSession);
			$em->flush();
			$this->addFlash(
				'notice',
				'Változtatások Elmentve!'
			);

			// this is where if there were calculated values, it would be necessary to query the object from the db again
			// and recreate the form to refresh the calculated values
		}

		return $this->render('signups/editAnnouncedSession.html.twig',
			[
				'announcedSession' => $announcedSession,
				'searchStart' => $searchStartDate,
				'searchDue' => $searchDueDate,
				'searchScheduleItemId' => $searchScheduleItemId,
				'form' => $form->createView(),
				'logged_in_user' => $loggedInUser
			]
		);
	}
}
