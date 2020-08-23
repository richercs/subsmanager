<?php
/**
 * Created by PhpStorm.
 * User: csabi
 * Date: 5/14/17
 * Time: 11:15 AM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\UserContact;
use AppBundle\Form\UserContactType;
use AppBundle\Repository\UserContactRepository;
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
use FOS\UserBundle\Util\PasswordUpdater;

class ApiController extends Controller
{

    /**
     * @Route("/api/userdata", name="userdata_get")
     *
     *
     * @param Request $request
     * @return Response
     */
    public function getUserDataAction(Request $request)
    {
        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

        $id = $loggedInUser->getId();

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

        /** @var AttendanceHistoryRepository $attendanceHistoryRepo */
        $attendanceHistoryRepo =$em->getRepository('AppBundle\Entity\AttendanceHistory');

        /** @var ArrayCollection $activeSubscriptions */
        $activeSubscriptions = new ArrayCollection();

        /** @var ArrayCollection $inactiveSubscriptions */
        $inactiveSubscriptions = new ArrayCollection();

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {

            $attendancRecords = $attendanceHistoryRepo->findBy(array('subscription' => $subscription->getId()));

            $attendancesLeft = $subscription->getAttendanceCount() - count($attendancRecords);

            if ($subscription->getStatusBoolean() && $attendancesLeft > 0) {

                $activeSubscriptions->add(array(
                    'id' => $subscription->getId(),
                    'owner_first_name' => $subscription->getOwner()->getFirstName(),
                    'owner_last_name' => $subscription->getOwner()->getLastName(),
                    'attendance_count' => $subscription->getAttendanceCount(),
                    'attendances_left' => $attendancesLeft,
                    'start_date_string' =>$subscription->getStartDateString(),
                    'due_date_string' =>$subscription->getDueDateString(),
                    'price' => $subscription->getPrice()
                ));

            } else {

                $inactiveSubscriptions->add(array(
                    'id' => $subscription->getId(),
                    'owner_first_name' => $subscription->getOwner()->getFirstName(),
                    'owner_last_name' => $subscription->getOwner()->getLastName(),
                    'attendance_count' => $subscription->getAttendanceCount(),
                    'attendances_left' => $attendancesLeft,
                    'start_date_string' =>$subscription->getStartDateString(),
                    'due_date_string' =>$subscription->getDueDateString(),
                    'price' => $subscription->getPrice()
                ));
            }

        }

        $response = new JsonResponse();

        return $response->setData(array(
            'userData' => array(
                'id' => $userAccount->getId(),
                'firstName' => $userAccount->getFirstName(),
                'lastName' =>$userAccount->getLastName(),
                'email' => $userAccount->getEmail(),
            ),
            'activeSubscriptions' => $activeSubscriptions->toArray(),
            'inactiveSubscriptions' => $inactiveSubscriptions->toArray(),
            'error' => null
        ));
    }

    /**
     * @Route("/api/subscriptiondata/{id}", name="subscriptiondata_get")
     *
     * @param integer $id
     * @param Request $request
     * @return Response
     */
    public function getSubscriptionDataAction($id, Request $request)
    {

        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

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

        $subscriptionsOwned = $subscriptionRepo->findBy(array('owner' => $loggedInUser->getId()));

        $subscriptionsOwnedCollection = new ArrayCollection();

        foreach ($subscriptionsOwned as $subscriptionOwned) {
            $subscriptionsOwnedCollection->add($subscriptionOwned);
        }

        if(!$subscriptionsOwnedCollection->contains($subscription)) {

            return new Response(null);
        }

        /** @var AttendanceHistoryRepository $attendanceHistoryRepo */
        $attendanceHistoryRepo =$em->getRepository('AppBundle\Entity\AttendanceHistory');

        $attendancRecords = $attendanceHistoryRepo->findBy(array('subscription' => $subscription->getId()));

        /** @var ArrayCollection $attendanceData */
        $attendanceData = new ArrayCollection();

        /** @var AttendanceHistory $attendancRecord */
        foreach ($attendancRecords as $attendancRecord) {
            $attendanceData->add(array(
                'session_type_name' => $attendancRecord->getSessionEvent()->getScheduleItem()->getSessionName(),
                'session_date' => $attendancRecord->getSessionEvent()->getSessionEventDate()->format('Y.m.d.'),
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
                'attendance_count' => $subscription->getAttendanceCount(),
                'start_date_string' => $subscription->getStartDateString(),
                'due_date_string' => $subscription->getDueDateString(),
                'price' => $subscription->getPrice()
            ),
            'attendanceData' => $attendanceData->toArray(),
            'error' => null
        ));
    }

    /**
     * @Route("/api/add_usercontact", name="api_add_usercontact")
     *
     *
     * @param Request $request
     * @return Response
     */
    public function addApiUserContactAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var PasswordUpdater $passwordHasher */
        $passwordHasher = $this->get('fos_user.util.password_updater');

        /** @var UserContactRepository $userContactRepository */
        $userContactRepository = $em->getRepository('AppBundle\Entity\UserContact');

        $newContact = new UserContact();

        $form = $this->createForm(new UserContactType(), $newContact);
        $form->handleRequest($request);

        if ($form->isValid())
        {

            $dummyUser = new UserAccount();
            $dummyUser->setPlainPassword($newContact->getPassword());

            $passwordHasher->hashPassword($dummyUser);

            $newContact->setPassword($dummyUser->getPassword());

            $em->persist($newContact);

            $em->flush();

            return $this->redirectToRoute('api_add_usercontact_success');
        }

        return $this->render('contacts/addEmbededUserContact.html.twig',
            array(
                'new_contact' => $newContact,
                'form' => $form->createView(),
            ));
    }

    /**
     * @Route("/api/add_usercontact_success", name="api_add_usercontact_success")
     *
     *
     * @param Request $request
     * @return Response
     */
    public function addApiUserContactSuccessAction(Request $request)
    {

        return $this->render('contacts/addEmbededUserContactSuccess.html.twig');
    }
}