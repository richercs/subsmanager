<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\SessionEvent;
use AppBundle\Entity\Subscription;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\ScheduleItemRepository;
use AppBundle\Repository\SessionEventRepository;
use AppBundle\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminStatsController extends Controller
{
	/**
	 * @Route("/admin_stats", name="admin_stats")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response|null
	 */
	public function viewAdminStatsAction(Request $request)
	{
		/** UserAccount $loggedInUser */
		$loggedInUser = $this->getUser();

		/** @var EntityManager $em */
		$em = $this->get('doctrine.orm.default_entity_manager');

		/** @var SessionEventRepository $sessionEventRepository */
		$sessionEventRepository = $em->getRepository('AppBundle\Entity\SessionEvent');

		$statsStartDate = $request->get('statsStart');

		$statsDueDate = $request->get('statsDue');

		$statsScheduleItemId = $request->get('statsScheduleItemId');

		if (is_null($statsScheduleItemId) || $statsScheduleItemId == "") {

			if (is_null($statsStartDate) && is_null($statsDueDate) || $statsStartDate == "" && $statsDueDate == "") {
				$events = $sessionEventRepository->getLastThirtySessions();
			} else {
				$events = $sessionEventRepository->getSessionsBetweenDates($statsStartDate, $statsDueDate);
			}
		} else {

			/** @var ScheduleItemRepository $scheduleItemRepository */
			$scheduleItemRepository = $em->getRepository(ScheduleItem::class);

			$filteredScheduleItem = $scheduleItemRepository->find($statsScheduleItemId);

			if (!$filteredScheduleItem) {
				$this->addFlash(
					'error',
					'Nincs ilyen azonosítójú órarendi elem: ' . $filteredScheduleItem . '!'
				);
			}

			if (is_null($statsStartDate) && is_null($statsDueDate) || $statsStartDate == "" && $statsDueDate == "") {
				$events = $sessionEventRepository->getLastThirtySessionsFilteredScheduleItem($filteredScheduleItem);
			} else {
				$events = $sessionEventRepository->getSessionsBetweenDatesFilteredScheduleItem($statsStartDate, $statsDueDate, $filteredScheduleItem);
			}
		}


		/** @var SessionEvent $event */
		foreach ($events as $event) {

			$revenue = $this->calculateRevenueAction($event);

			$event->setRevenue($revenue);
		}

		$eventCount = count($events);

		$totalRevenue = 0;

		$totalAttendeeCount = 0;

		/** @var SessionEvent $event */
		foreach ($events as $event) {

			$totalRevenue = $totalRevenue + $event->getRevenue();

			$totalAttendeeCount = $totalAttendeeCount + $event->getAttendeeFullCount();
		}

		/** @var ScheduleItemRepository $scheduleItemRepository */
		$scheduleItemRepository = $em->getRepository('AppBundle\Entity\ScheduleItem');

		$scheduleItems = $scheduleItemRepository->findAll();

		return $this->render('stats/viewAdminStats.html.twig', array(
			'events' => $events,
			'event_count' => $eventCount,
			'total_revenue' => $totalRevenue,
			'total_attendee_count' => $totalAttendeeCount,
			'schedule_items' => $scheduleItems,
			'logged_in_user' => $loggedInUser
		));
	}

	/**
	 * Calculates the revenue of one session event.
	 *
	 * @param SessionEvent $sessionEvent
	 * @return int
	 */
	public function calculateRevenueAction($sessionEvent)
	{
		$resultRevenue = 0;

		/** @var AttendanceHistory $attendance */
		foreach ($sessionEvent->getAttendees() as $attendance) {

			if (!is_null($attendance->getSubscription())) {
				$recordRevenue = 0;

				switch ($attendance->getSubscription()->getSubscriptionType()) {
					case Subscription::SUBSCRIPTION_TYPE_CREDIT:
						$creditUnitPrice = $attendance->getSubscription()->getPrice() / $attendance->getSubscription()->getCredit();
						$recordRevenue = $creditUnitPrice * $sessionEvent->getSessionCreditRequirement();
						break;
					case Subscription::SUBSCRIPTION_TYPE_ATTENDANCE:
						$recordRevenue = $attendance->getSubscription()->getPrice() / $attendance->getSubscription()->getAttendanceCount();
						break;
				}
				$resultRevenue = $resultRevenue + round($recordRevenue);
			}
		}

		return $resultRevenue + $sessionEvent->getSessionFeeRevenueSold();
	}

	/**
	 * @Route("/admin_stats/subscription", name="admin_stats_subscriptions")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response|null
	 */
	public function viewAdminStatsOnSubscriptionsAction(Request $request)
	{
		/** UserAccount $loggedInUser */
		$loggedInUser = $this->getUser();

		/** @var EntityManager $em */
		$em = $this->get('doctrine.orm.default_entity_manager');

		/** @var SubscriptionRepository $subscriptionRepo */
		$subscriptionRepo = $em->getRepository('AppBundle\Entity\Subscription');

		$statsStartDate = $request->get('statsStart');

		$statsDueDate = $request->get('statsDue');

		if (is_null($statsStartDate) && is_null($statsDueDate) || $statsStartDate == "" && $statsDueDate == "") {
			$subscriptions = $subscriptionRepo->getLastFiftySubscriptions();
		} else {
			$subscriptions = $subscriptionRepo->getSubscriptionsBetweenDates($statsStartDate, $statsDueDate);
		}

		/** @var AttendanceHistoryRepository $attendanceHistoryRepo */
		$attendanceHistoryRepo = $em->getRepository(AttendanceHistory::class);

		$subscriptionsCount = count($subscriptions);

		$totalRevenue = 0;

		$totalUsageCount = 0;

		/** @var Subscription $subscription */
		foreach ($subscriptions as $subscription) {

			$totalRevenue = $totalRevenue + $subscription->getPrice();

			$usageCount = count($attendanceHistoryRepo->findBy(array('subscription' => $subscription)));

			$subscription->setUsages($usageCount);

			$totalUsageCount = $totalUsageCount + $usageCount;
		}

		return $this->render('stats/viewAdminStatsOnSubscriptions.html.twig', array(
			'subscriptions' => $subscriptions,
			'subscription_count' => $subscriptionsCount,
			'total_revenue' => $totalRevenue,
			'total_usage_count' => $totalUsageCount,
			'logged_in_user' => $loggedInUser
		));
	}
}
