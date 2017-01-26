<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\SessionData;
use AppBundle\Entity\UserAccount;
use AppBundle\Entity\Subscription;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserAccountRepository;
use AppBundle\Form\UserAccountType;
use AppBundle\Form\SubscriptionType;
use DateTime;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{


//    /**
//     * @Route("/dummy/{name}", name="dummy")
//     */
//    public function dummyAction(Request $request)
//    {
//        /** @var EntityManager $em */
//        $em = $this->get('doctrine.orm.default_entity_manager');
//
//        /** @var SubscriptionRepository $sr */
//        $sr = $em->getRepository('AppBundle\Entity\Subscription');
//
//        $a = $sr->getRunningSubs();
//
//        dump($a);
//        die;
//    }

    /**
     * @Route("/add_subscription", name="add_subscription")
     *
     * @param Request request
     * @return array
     */
    public function addSubscriptionAction(Request $request)
    {
//        /** @var UserAccount $loggedInUser */
//        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $new_subscription = new Subscription();

        $form = $this->createForm(new SubscriptionType(), $new_subscription);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($new_subscription);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('add_subsscription');
        }

        return $this->render('subscription/addSubscription.html.twig',
            array(
                'new_subscription' => $new_subscription,
                'form' => $form->createView()
            ));

    }

    /**
     * @Route("/add_user_form", name="add_user_account")
     *
     * @param Request request
     * @return array
     */
    public function addUserAccountAction(Request $request)
    {
//        /** @var UserAccount $loggedInUser */
//        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $new_user = new UserAccount();

        $form = $this->createForm(new UserAccountType(), $new_user);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($new_user);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('add_user_account');
        }

        return $this->render('users/addUserAccount.html.twig',
            array(
                'new_user' => $new_user,
                'form' => $form->createView()
            ));

    }



    /**
     * @Route("/fixture/attendance", name="fixtureAttendance")
     */
    public function fixtureAttendanceAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        $genUser = new UserAccount();
        $genUser->setFirstName('Csaba');
        $genUser->setLastName('Richer');

        $em->persist($genUser);
        $em->flush();

        $schedule_item = new ScheduleItem();
        $schedule_item->setLocation('Vitál');
        $schedule_item->setScheduledDate(DateTime::createFromFormat('Y-m-d', '2016-01-01'));

        $em->persist($schedule_item);
        $em->flush();

        $subscription = new Subscription();
        $subscription->setIsMonthlyTicket(0);
        $subscription->setAttendee($genUser);

        $em->persist($subscription);
        $em->flush();
    }
}
