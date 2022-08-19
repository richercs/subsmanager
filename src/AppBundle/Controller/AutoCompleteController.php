<?php

namespace AppBundle\Controller;


use AppBundle\Entity\AttendanceHistory;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Entity\UserContact;
use AppBundle\Repository\AttendanceHistoryRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserAccountRepository;
use AppBundle\Repository\UserContactRepository;
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
	 * @param Request $request
	 * @return Response|null
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
			if ($user->isDeleted()) {
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
	 * @param $id
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
	 * @param Request $request
	 * @return Response
	 */
	public function loadSubscriptionRecord(Request $request)
	{
		$ownerId = $request->get('owner_id');

		/** @var SubscriptionRepository $repository */
		$repository = $this->get('doctrine.orm.default_entity_manager')->getRepository(Subscription::class);

		// TODO: Nem adja majd vissza csak a krediteseket, tehát egy régi session event NEM LESZ SZERKESZTHETŐ
		$subscriptions = $repository->findUsableSubscriptions();

		$responseArray = [];

		$ownerSet = false;

		/** @var Subscription $subscription */
		foreach ($subscriptions as $subscription) {

			if (!$ownerSet) {
				$responseArray[] = array(
					'id' => $subscription->getId(),
					'label' => (string)$subscription,
					'owner' => $subscription->getOwner()->getId(),
					'is_owned' => ($ownerId == $subscription->getOwner()->getId())
				);

				if (($ownerId == $subscription->getOwner()->getId())) {

					$ownerSet = true;
				}
			} else {
				$responseArray[] = array(
					'id' => $subscription->getId(),
					'label' => (string)$subscription,
					'owner' => $subscription->getOwner()->getId(),
					'is_owned' => ($ownerId == $subscription->getOwner()->getId()) && !$ownerSet
				);
			}
		}

		$response = new JsonResponse();

		return $response->setData($responseArray);
	}

	/**
	 * @Route("/fill_out_selected_user", name="fill_out_selected_user")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function fillOutSelectedUser(Request $request)
	{

		$selectedUserId = $request->get('selectFieldValue');

		$userContactId = $request->get('userContactId');

		/** @var EntityManager $em */
		$em = $this->get('doctrine.orm.default_entity_manager');

		/** @var UserAccountRepository $userAccountRepository */
		$userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

		/** @var UserAccount $selectedUserAccount */
		$selectedUserAccount = $userAccountRepository->find($selectedUserId);

		/** @var UserContactRepository $userContactRepository */
		$userContactRepository = $em->getRepository('AppBundle\Entity\UserContact');

		/** @var UserContact $userContact */
		$userContact = $userContactRepository->find($userContactId);

		$response = new JsonResponse();

		if (is_null($selectedUserAccount) || is_null($userContact)) {
			return $response;
		}

		return $response->setData(array(
			'user' => array(
				'username' => (string)$selectedUserAccount->getUsername(),
			),
			'contact' => array(
				'firstname' => (string)$userContact->getContactFirstName(),
				'lastname' => (string)$userContact->getContactLastName(),
				'email' => (string)$userContact->getContactEmail()
			)
		));
	}

	/**
	 * @Route("/loadSubscriptionInfo", name="load_subscription_info")
	 *
	 * @Security("has_role('ROLE_ADMIN')")
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function loadSubscriptionInfo(Request $request)
	{
		$subscriptionId = $request->get('subscription_id');

		/** @var SubscriptionRepository $repository */
		$repository = $this->get('doctrine.orm.default_entity_manager')->getRepository(Subscription::class);

		/** @var Subscription $subscription */
		$subscription = $repository->find($subscriptionId);

		/** @var AttendanceHistoryRepository $attendanceHistoryRepository */
		$attendanceHistoryRepository = $this->get('doctrine.orm.default_entity_manager')->getRepository(AttendanceHistory::class);

		$attendances = $attendanceHistoryRepository->findBy(array('subscription' => $subscriptionId));

		$countOfSubscriptionUsages = count($attendances);

		$creditUsage = 0;
		/** @var AttendanceHistory $attendance */
		foreach ($attendances as $attendance) {
			// TODO: duplicate subscription usages on session events count here as double tax on credit
			$creditUsage += $attendance->getSessionEvent()->getSessionCreditRequirement();
		}

		$response = new JsonResponse();

		return $response->setData(array(
			'id' => $subscription->getId(),
			'subscription_type' => $subscription->getSubscriptionType(),
			'subscription_credit' => $subscription->getCredit(),
			'subscription_credit_left' => $subscription->getCredit() - $creditUsage,
			'attendance_limit' => $subscription->getAttendanceCount(),
			'attendance_left' => ($subscription->getAttendanceCount() - $countOfSubscriptionUsages),
			'attendance_count' => $countOfSubscriptionUsages
		));
	}
}
