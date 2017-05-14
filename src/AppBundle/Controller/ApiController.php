<?php
/**
 * Created by PhpStorm.
 * User: csabi
 * Date: 5/14/17
 * Time: 11:15 AM
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\SessionEvent;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\ScheduleItemRepository;
use AppBundle\Repository\SessionEventRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ApiController extends Controller
{

    /**
     * @Route("/api/userdata/{id}", name="userdata_get")
     *
     *
     * @param Request request
     * @param id
     * @return Response
     */
    public function getUserDataAction($id, Request $request)
    {

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        /** @var UserAccount $userAccount */
        $userAccount = $userAccountRepository->find($id);

        if (!$userAccount) {

            $response = new JsonResponse();

            return $response->setData(array(
                'id' => null,
                'error' => 'Nincs ilyen azonosítójú felhasználó: ' . $id . '!'
            ));
        }

        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $em->getRepository('AppBundle\Entity\Subscription');

        $subscriptions = $subscriptionRepository->findBy(array('owner' => $userAccount->getId()));

        /** @var ArrayCollection $subscriptionDatas */
        $subscriptionDatas = new ArrayCollection();

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $subscriptionDatas->add(array(
                'id' => $subscription->getId(),
                'owner_first_name' => $subscription->getOwner()->getFirstName(),
                'owner_last_name' => $subscription->getOwner()->getLastName(),
                'attendance_count' => $subscription->getAttendanceCount(),
                'start_date_string' =>$subscription->getStartDateString(),
                'price' => $subscription->getPrice()
            ));
        }

        $response = new JsonResponse();

        return $response->setData(array(
            'userData' => array(
                'id' => $userAccount->getId(),
                'firstName' => $userAccount->getFirstName(),
                'lastName' =>$userAccount->getLastName(),
                'email' => $userAccount->getEmail(),
            ),
            'subscriptionsData' => $subscriptionDatas->toArray(),
            'error' => null
        ));
    }

    /**
     * @Route("/api/subscriptiondata/{id}", name="subscriptiondata_get")
     *
     *
     * @param Request request
     * @param id
     * @return Response
     */
    public function getSubscriptionDataAction($id, Request $request)
    {

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SubscriptionRepository $subscriptionRepo */
        $subscriptionRepo = $em->getRepository('AppBundle\Entity\Subscription');

        /** @var Subscription $subscription */
        $subscription = $subscriptionRepo->find($id);

        if (!$subscription) {
            $response = new JsonResponse();

            return $response->setData(array(
                'id' => null,
                'error' => 'Nincs ilyen azonosítójú bérlet: ' . $id . '!'
            ));
        }

        /** @var AttendanceHistoryRepository $attendanceHistoryRepo */
        $attendanceHistoryRepo =$em->getRepository('AppBundle\Entity\AttendanceHistory');

        $attendancRecords = $attendanceHistoryRepo->findBy(array('subscription' => $subscription->getId()));

        /** @var ArrayCollection $attendanceDatas */
        $attendanceDatas = new ArrayCollection();

        /** @var AttendanceHistory $attendancRecord */
        foreach ($attendancRecords as $attendancRecord) {
            $attendanceDatas->add(array(
                'session_type_name' => $attendancRecord->getSessionEvent()->getScheduleItem()->getSessionName(),
                'session_date' => $attendancRecord->getSessionEvent()->getSessionEventDateString(),
                'session_attendee_first_name' => $attendancRecord->getAttendee()->getFirstName(),
                'session_attendee_last_name' => $attendancRecord->getAttendee()->getLastName()
            ));
        }

        $response = new JsonResponse();

        return $response->setData(array(
            'subscriptionData' => array(
                'id' => $subscription->getId(),
                'owner_first_name' => $subscription->getOwner()->getFirstName(),
                'owner_last_name' => $subscription->getOwner()->getLastName(),
                'buyer_first_name' => $subscription->getBuyer()->getFirstName(),
                'buyer_last_name' => $subscription->getBuyer()->getLastName(),
                'attendance_count' => $subscription->getAttendanceCount(),
                'start_date_string' => $subscription->getStartDateString(),
                'due_date_string' => $subscription->getDueDateString(),
                'price' => $subscription->getPrice()
            ),
            'attendanceDatas' => $attendanceDatas->toArray(),
            'error' => null
        ));
    }
}