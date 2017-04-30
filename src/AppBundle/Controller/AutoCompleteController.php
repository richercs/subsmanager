<?php

namespace AppBundle\Controller;


use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AutoCompleteController extends Controller
{
    /**
     * @Route("/useraccount_search", name="useraccount_search")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function searchUserAccountAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $term = $request->query->get('term'); // use "term" instead of "q" for jquery-ui

        /** @var array $results */
        $results = $userAccountRepository->findLikeUserName($term);

        // Do not suggest deleted user accounts
        /** @var UserAccount $user */
        foreach ($results as $key => $user) {
            if($user->isDeleted()) {
                unset($results[$key]);
            }
        }

        return $this->render('event/attendeeSearch.twig', array(
            'results' => $results
        ));
    }

    /**
     * @Route("/useraccount_get/{id}", name="useraccount_get")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return Response
     */
    public function getUserAccountAction($id = null)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        /** @var UserAccount $useraccount */
        $useraccount = $userAccountRepository->find($id);

        return new Response($useraccount->getUsername());
    }


    /**
     * @Route("/loadSubscription", name="load_subscription_record")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return Response
     */
    public function loadSubscriptionRecord(Request $request)
    {
        $ownerId = $request->get('owner_id');

        /** @var SubscriptionRepository $repository */
        $repository = $this->get('doctrine.orm.default_entity_manager')->getRepository(Subscription::class);

        $subscriptions = $repository->findUsableSubscriptions();

        $responseArray = [];

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {
            $responseArray[] = array(
                'id' => $subscription->getId(),
                'label' => (string)$subscription,
                'owner' => $subscription->getOwner()->getId(),
                'is_owned' => ($ownerId == $subscription->getOwner()->getId())
            );
        }

        $response = new JsonResponse();

        return $response->setData($responseArray);
    }


    /**
     * @Route("/fill_out_selected_user", name="fill_out_selected_user")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return Response
     */
    public function fillOutSelectedUser(Request $request) {
        $selectedUserId = $request->get('selectFieldValue');

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        /** @var UserAccount $selectedUserAccount */
        $selectedUserAccount = $userAccountRepository->find($selectedUserId);

        $response = new JsonResponse();

        if (is_null($selectedUserAccount)) {
            return $response;
        }

        return $response->setData(array(
            'firstname' => (string) $selectedUserAccount->getFirstName(),
            'lastname' => (string) $selectedUserAccount->getLastName(),
            'email' => (string) $selectedUserAccount->getEmail(),
            'username' => (string) $selectedUserAccount->getUsername(),
        ));
    }

    /**
     * @Route("/loadSubscriptionInfo", name="load_subscription_info")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return Response
     */
    public function loadSubscriptionInfo(Request $request)
    {
        $subscriptionId = $request->get('subscription_id');

        /** @var SubscriptionRepository $repository */
        $repository = $this->get('doctrine.orm.default_entity_manager')->getRepository(Subscription::class);

        /** @var Subscription $subscription */
        $subscription = $repository->find($subscriptionId);

        /** @var AttendanceHistoryRepository $attendaceHistoryRepo */
        $attendaceHistoryRepo = $this->get('doctrine.orm.default_entity_manager')->getRepository(AttendanceHistory::class);

        $subscriptionUsages = $attendaceHistoryRepo->findBy(array('subscription' => $subscriptionId));

        $countOfSubscriptionUsages = count($subscriptionUsages);

        $response = new JsonResponse();

        return $response->setData(array(
            'id' => $subscription->getId(),
            'attendance_limit' => $subscription->getAttendanceCount(),
            'attendance_left' => ($subscription->getAttendanceCount() - $countOfSubscriptionUsages),
            'attendance_count' => $countOfSubscriptionUsages
        ));
    }
}