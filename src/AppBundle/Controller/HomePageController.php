<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManager;
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

        $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $user_repo */
        $user_repo = $em->getRepository('AppBundle\Entity\UserAccount');
        $subscription_repo = $em->getRepository('AppBundle\Entity\Subscription');
        $scheduleItem_repo = $em->getRepository('AppBundle\Entity\ScheduleItem');
        $sessionEvent_repo = $em->getRepository('AppBundle\Entity\SessionEvent');

        $count_users = count($user_repo->findAll());
        $count_subscriptions = count($subscription_repo->findAll());
        $count_scheduleItems = count($scheduleItem_repo->findAll());
        $count_sessionEvents = count($sessionEvent_repo->findAll());

        return $this->render('default/index.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'count_users' => $count_users,
                'count_subscriptions' => $count_subscriptions,
                'count_scheduleItems' => $count_scheduleItems,
                'count_sessionEvents' => $count_sessionEvents
            ));
    }

}