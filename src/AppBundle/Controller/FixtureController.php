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

class FixtureController extends Controller
{



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
        $subscription->setOwner($genUser);

        $em->persist($subscription);
        $em->flush();
    }
}
