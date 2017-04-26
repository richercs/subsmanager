<?php
/**
 * Created by PhpStorm.
 * User: csabi
 * Date: 4/23/17
 * Time: 5:18 PM
 */

namespace AppBundle\Controller;



use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\SessionEvent;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\SessionEventRepository;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param Request request
     * @return array
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

        if(is_null($statsStartDate) && is_null($statsDueDate) || $statsStartDate == "" && $statsDueDate == "") {
            $events = $sessionEventRepository->getLastFiftySessions();
        } else {
            $events = $sessionEventRepository->getSessionsBetweenDates($statsStartDate, $statsDueDate);
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

        return $this->render('stats/viewAdminStats.html.twig', array(
            'events' => $events,
            'event_count' => $eventCount,
            'total_revenue' => $totalRevenue,
            'total_attendee_count' => $totalAttendeeCount,
            'logged_in_user' => $loggedInUser
        ));
    }

    /**
     * Calculates the revenue of one session event.
     *
     * @param SessionEvent $sessionEvent
     * @return int
     */
    public function calculateRevenueAction($sessionEvent) {

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var AttendanceHistoryRepository $attendanceHistoryRepo */
        $attendanceHistoryRepo = $em->getRepository('AppBundle\Entity\AttendanceHistory');

        $resultRevenue = 0;

        /** @var AttendanceHistory $record*/
        foreach ($sessionEvent->getAttendees() as $record) {

            /** @var ArrayCollection $subscriptionUsages */
            $subscriptionUsages = $attendanceHistoryRepo->findBy(array('subscription' => $record->getSubscription()));

            $count = count($subscriptionUsages);

            $recordRevenue = $record->getSubscription()->getPrice() / $count;

            $resultRevenue = $resultRevenue + round($recordRevenue,0);
        }

        $resultRevenue = $resultRevenue + $sessionEvent->getSessionFeeRevenueSold();

        return $resultRevenue;
    }

}