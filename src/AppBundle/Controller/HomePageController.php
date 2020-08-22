<?php

namespace AppBundle\Controller;


use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\UserAccount;
use AppBundle\Entity\UserContact;
use AppBundle\Repository\AnnouncedSessionRepository;
use AppBundle\Repository\ScheduleItemRepository;
use AppBundle\Repository\SessionEventRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomePageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param Request request
     * @return array
     */
    public function showAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        if(!is_null($loggedInUser) && !$loggedInUser->getIsAdmin()) {
            return $this->render('default/loading.html.twig',
                array(
                    'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                    'logged_in_user' => $loggedInUser,
                ));
        }

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        $this->get('session')->set('pending_user_contact_count', $this->getPendigUserCount());

        /** @var UserAccountRepository $user_repo */
        $user_repo = $em->getRepository('AppBundle\Entity\UserAccount');
        /** @var SubscriptionRepository $subscription_repo */
        $subscription_repo = $em->getRepository('AppBundle\Entity\Subscription');
        /** @var ScheduleItemRepository $scheduleItem_repo */
        $scheduleItem_repo = $em->getRepository('AppBundle\Entity\ScheduleItem');
        /** @var SessionEventRepository $sessionEvent_repo */
        $sessionEvent_repo = $em->getRepository('AppBundle\Entity\SessionEvent');

        $count_users = count($user_repo->findAll());
        $count_subscriptions = count($subscription_repo->findAll());
        $count_scheduleItems = count($scheduleItem_repo->findAll());
        $count_sessionEvents = count($sessionEvent_repo->findAll());

        $scheduleItemsOrdered = $scheduleItem_repo->getOrderedScheduleItems();

        /** @var AnnouncedSessionRepository $announcedSessionRepository */
        $announcedSessionRepository = $em->getRepository('AppBundle\Entity\AnnouncedSession');

        $availableSessions = $announcedSessionRepository->findBy(array("sessionEvent" => null));

        // Set extra info needed to list entities
        /** @var AnnouncedSession $announcedSession */
        foreach ($availableSessions as $announcedSession) {
            $announcedSession->calculateNumberOfSignees();
        }

        /** @var ScheduleItem $scheduleItem */
        foreach ($scheduleItemsOrdered as $key => $scheduleItem) {
            if($scheduleItem->isDeleted()) {
                unset($scheduleItemsOrdered[$key]);
            }
        }

        return $this->render('default/index.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'count_users' => $count_users,
                'count_subscriptions' => $count_subscriptions,
                'count_scheduleItems' => $count_scheduleItems,
                'count_sessionEvents' => $count_sessionEvents,
                'logged_in_user' => $loggedInUser,
                'ordered_schedule_items' => $scheduleItemsOrdered,
                'count_scheduleItems_active' => count($scheduleItemsOrdered),
                'available_sessions' => $availableSessions ? $availableSessions : null
            ));
    }

    public function getPendigUserCount()
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserContactRepository $userContactRepository */
        $userContactRepository = $em->getRepository(UserContact::class);

        return $userContactRepository->getPendingCount();
    }

    /**
     * @Route("/login_faliure", name="login_faliure")
     *
     * @param Request request
     *
     * @return RedirectResponse
     */
    public function executeLoginFaliure()
    {
        return new RedirectResponse($this->getParameter('login_faliure'));
    }
}