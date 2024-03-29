<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\SubscriptionType;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SubscriptionController extends Controller
{
	/**
	 * @Route("/subscription/list_all", name="subscription_list_all")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response|null
	 * @throws Exception
	 */
	public function listAllSubscriptionsAction(Request $request)
	{
		/** @var UserAccount $loggedInUser */
		$loggedInUser = $this->getUser();

		/** @var EntityManager $em */
		$em = $this->get('doctrine.orm.default_entity_manager');

		/** @var SubscriptionRepository $subscriptionRepository */
		$subscriptionRepository = $em->getRepository('AppBundle\Entity\Subscription');

		$subscriptions = $subscriptionRepository->findAll();

		$activeSubs = new ArrayCollection();
		$nonActiveSubs = new ArrayCollection();

		/** @var Subscription $subscription */
		foreach ($subscriptions as $subscription) {
			if ($subscription->getStatusBoolean()) {
				$activeSubs->add($subscription);
			} else {
				$nonActiveSubs->add($subscription);
			}
		}

		$this->calculateUsageAndCredit($activeSubs);
		$this->calculateUsageAndCredit($nonActiveSubs);

		// SORTING - Active subscription sorted by the remaining attendance counts
		$activeSubsIterator = $activeSubs->getIterator();

		$activeSubsIterator->uasort(function ($first, $second) {
			/** @var Subscription $first */
			/** @var Subscription $second */
			return $first->getCurrentCredit() > $second->getCurrentCredit() ? 1 : -1;
		});

		$activeSubs = new ArrayCollection(iterator_to_array($activeSubsIterator));

		// SORTING - Non-active subscriptions sorted by the due date
		$nonActiveSubsIterator = $nonActiveSubs->getIterator();

		$nonActiveSubsIterator->uasort(function ($first, $second) {
			/** @var Subscription $first */
			/** @var Subscription $second */
			return $first->getDueDate() > $second->getDueDate() ? -1 : 1;
		});

		$nonActiveSubs = new ArrayCollection(array_slice(iterator_to_array($nonActiveSubsIterator), 0, 50));

		return $this->render('subscription/listAllSubscriptions.html.twig',
			[
				'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
				'subscriptions' => $subscriptions,
				'active_subscriptions' => $activeSubs,
				'non_active_subscriptions' => $nonActiveSubs,
				'logged_in_user' => $loggedInUser
			]
		);
	}

	/**
	 * @param ArrayCollection $subscriptionList
	 * @return void
	 */
	private function calculateUsageAndCredit($subscriptionList)
	{
		/** @var EntityManager $em */
		$em = $this->get('doctrine.orm.default_entity_manager');

		/** @var AttendanceHistoryRepository $attendanceHistoryRepo */
		$attendanceHistoryRepo = $em->getRepository(AttendanceHistory::class);

		/** @var Subscription $subscription */
		foreach ($subscriptionList as $subscription) {

			$attendances = $attendanceHistoryRepo->findBy(array('subscription' => $subscription));

			$creditUsage = 0;
			/** @var AttendanceHistory $attendance */
			foreach ($attendances as $attendance) {
				$creditUsage += $attendance->getSessionEvent()->getSessionCreditRequirement();
			}

			$subscription->setUsages($subscription->getAttendanceCount() - count($attendances));
			$subscription->setCurrentCredit($subscription->getCredit() - $creditUsage);
		}
	}

	/**
	 * @Route("/subscription/add_subscription", name="subscription_add_subscription")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response|RedirectResponse|null
	 * @throws OptimisticLockException
	 */
	public function addSubscriptionAction(Request $request)
	{
		/** @var UserAccount $loggedInUser */
		$loggedInUser = $this->getUser();

		/** @var EntityManager $em */
		$em = $this->get('doctrine.orm.default_entity_manager');

		$newSubscription = new Subscription();
		$newSubscription->setSubscriptionType(Subscription::SUBSCRIPTION_TYPE_CREDIT);

		$form = $this->createForm(new SubscriptionType($newSubscription), $newSubscription);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$em->persist($newSubscription);
			$em->flush();
			$this->addFlash(
				'notice',
				'Változtatások Elmentve!'
			);

			return $this->redirectToRoute('subscription_add_subscription');
		}

		return $this->render('subscription/addSubscription.html.twig',
			[
				'new_subscription' => $newSubscription,
				'form' => $form->createView(),
				'logged_in_user' => $loggedInUser
			]
		);
	}

	/**
	 * Opens edit page for subscriptions with passed $id.
	 *
	 * @Route("/subscription/{id}", name="subscription_edit_subscription", defaults={"id" = -1})
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param $id
	 * @param Request $request
	 * @param $breakEventId
	 * @return RedirectResponse|Response|null
	 * @throws OptimisticLockException
	 */
	public function editSubscriptionAction($id, Request $request, $breakEventId = null)
	{
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

		$form = $this->createForm(new SubscriptionType($subscription), $subscription);
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

			if (!is_null($userAccountIDFromGet)) {

				return $this->render('subscription/editSubscription.html.twig',
					[
						'subscription' => $subscription,
						'user_account_id' => intval($userAccountIDFromGet),
						'form' => $form->createView(),
						'break_event_id' => $breakEventId,
						'logged_in_user' => $loggedInUser
					]
				);
			}

			return $this->render('subscription/editSubscription.html.twig',
				[
					'subscription' => $subscription,
					'form' => $form->createView(),
					'break_event_id' => $breakEventId,
					'logged_in_user' => $loggedInUser
				]
			);
		}

		$userAccountIDFromGet = $request->get('user_account_id');

		if (!is_null($userAccountIDFromGet)) {

			return $this->render('subscription/editSubscription.html.twig',
				[
					'subscription' => $subscription,
					'user_account_id' => intval($userAccountIDFromGet),
					'form' => $form->createView(),
					'break_event_id' => $breakEventId,
					'logged_in_user' => $loggedInUser
				]
			);
		}

		return $this->render('subscription/editSubscription.html.twig',
			[
				'subscription' => $subscription,
				'form' => $form->createView(),
				'break_event_id' => $breakEventId,
				'logged_in_user' => $loggedInUser
			]
		);
	}

	/**
	 * @Route("/subscription/view_attendances/{id}", name="subscription_view_attendances", defaults={"id" = -1})
	 *
	 * @param $id
	 * @param Request $request
	 * @return RedirectResponse|Response|null
	 */
	public function viewSubscriptionAttendances($id, Request $request)
	{
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
		$attendanceHistoryRepo = $em->getRepository('AppBundle\Entity\AttendanceHistory');

		$attendanceRecords = $attendanceHistoryRepo->getAttendancesOfSubscriptionOrderedBySessionEventDate($subscription);

		return $this->render('subscription/viewSubscriptionAttendances.html.twig',
			[
				'subscription' => $subscription,
				'attendances' => $attendanceRecords,
				'logged_in_user' => $loggedInUser
			]
		);
	}

	/**
	 * Check if the subscription is in any session events
	 *
	 * @Route("/subscription/check_sessions/{id}", name="subscription_check_sessionsevents", defaults={"id" = -1})
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param $id
	 * @param Request $request
	 * @return RedirectResponse|Response|null
	 * @throws OptimisticLockException
	 */
	public function checkSubscriptionInSessionEventsAction($id, Request $request)
	{
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
