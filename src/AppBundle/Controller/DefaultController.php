<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SessionData;
use AppBundle\Entity\UserAccount;
use AppBundle\Entity\Subscription;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserAccountRepository;
use AppBundle\Form\UserAccountType;
use DateTime;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Tests\Fixtures\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need

        $name = $request->get('name');

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        $user_account = new UserAccount();

        $user_account->setFirstName($name);

        $user_account->setLastName(strrev($name));

        $em->persist($user_account);

        $em->flush();

        /** @var UserAccountRepository $user_repo */
        $user_repo = $em->getRepository('AppBundle\Entity\UserAccount');

        $users = $user_repo->getAllWithNameShorterThen();

        /** @var UserAccount $user */
        foreach ($users as $user) {
            $user->setLastName("rövid volt");

            $em->persist($user);
        }

        $em->flush();

        return $this->render('default/index.html.twig',
            array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'users' => $users
        ));
    }

    /**
     * @Route("/dummy/{name}", name="dummy")
     */
    public function dummyAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SubscriptionRepository $sr */
        $sr = $em->getRepository('AppBundle\Entity\Subscription');

        $a = $sr->getRunningSubs();

        dump($a);
        die;
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
            return $this->redirectToRoute('homepage',array('name' => 'CsabiValid'));
        }

        return $this->render('UserAccount/addUserAccount.html.twig',
            array(
                //'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
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

        $session = new SessionData();
        $session->setLocation('Vitál');
        $session->setScheduledDate(DateTime::createFromFormat('Y-m-d', '2016-01-01'));

        $em->persist($session);
        $em->flush();

        $subscription = new Subscription();
        $subscription->setIsMonthlyTicket(0);
        $subscription->setAttendee($genUser);

        $em->persist($subscription);
        $em->flush();
    }
}
