<?php


namespace AppBundle\Controller;



use AppBundle\Entity\BreakEvent;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\BreakEventType;
use AppBundle\Repository\BreakEventRepository;
use AppBundle\Repository\SubscriptionRepository;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class BreakEventController extends Controller
{
    /**
     * @Route("/breakevent/list_all", name="break_event_list_all")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function listAllBreakEventsAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var BreakEventRepository $breakEventRepo */
        $breakEventRepo = $em->getRepository('AppBundle\Entity\BreakEvent');

        $breaks = $breakEventRepo->findAll();

        return $this->render('break/listAllBreakEvents.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'breaks' => array_reverse($breaks),
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("/breakevent/add_breakevent", name="break_add_breakevent")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addBreakEventAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var BreakEventRepository $breakEventRepository */
        $breakEventRepository = $em->getRepository('AppBundle\Entity\BreakEvent');

        $newBreak = new BreakEvent();

        $form = $this->createForm(new BreakEventType(), $newBreak);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $breakEvents = $breakEventRepository->findAll();

            /** @var BreakEvent $existingBreakEvent */
            foreach ($breakEvents as $existingBreakEvent) {

                if ($newBreak->hasSameDay($existingBreakEvent)) {

                    $this->addFlash(
                        'error',
                        'Erre a napra már meg van hírdetve szünet!' . PHP_EOL .
                        'Szünet azonosító: ' . $existingBreakEvent->getId()
                    );

                    return $this->redirectToRoute('break_add_breakevent');
                }

            }

            return $this->redirectToRoute('breakevent_check_and_extend_subscriptions', array(
                'break_event_day' => $newBreak->getBreakEventDay()->format('Y-m-d H:i'),
            ));
        }

        return $this->render('break/addBreakEvent.html.twig',
            array(
                'new_break' => $newBreak,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));

    }

    /**
     * Opens edit page for break events with passed $id.
     *
     * @Route("/break/{id}", name="break_edit_break_event", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function editBreakEventAction($id, Request $request) {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var BreakEventRepository $breakEventRepo */
        $breakEventRepo = $em->getRepository('AppBundle\Entity\BreakEvent');

        // Editing break event
        /** @var BreakEvent $breakEvent */
        $breakEvent = $breakEventRepo->find($id);

        if (!$breakEvent) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú szünet esemény: ' . $id . '!'
            );
            return $this->redirectToRoute('break_event_list_all');
        }

        /** @var Form $form */
        $form = $this->createFormBuilder()
            ->add('breakData', 'text', array(
                'disabled' => true,
                'label' => 'Szünet Napja',
                'data' => $breakEvent->getBreakEventDay()->format('Y.m.d.'),
            ))
            ->add('save', 'submit', array(
                'attr' => array( 'class' => 'btn btn-primary'),
                'label' => 'Hosszabbítások Ellenőrzése'
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            return $this->redirectToRoute('breakevent_check_and_revert_subscriptions', array(
                'id' => $breakEvent->getId(),
            ));
        }

        return $this->render('break/editBreakEvent.html.twig',
            array(
                'break_event' => $breakEvent,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * Check if the break event with passed $id clashes with active period of any subscription.
     * Save the extended due dates to the database if the Save form is valind and sent.
     *
     * @Route("/breakevent/check_and_extend_subs", name="breakevent_check_and_extend_subscriptions")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return array
     */
    public function checkAndExtendSubscriptionAndBreakEventsAction(Request $request) {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        if (!is_null($request->query->get('break_event_day'))) {

            $breakEventDay = $request->query->get('break_event_day');

        } else {

            // message
            $this->addFlash(
                'error',
                'Hiányzó szünet nap!'
            );

            // show list
            return $this->redirectToRoute('break_event_list_all');
        }

        $breakEvent = new BreakEvent();

        $breakEventDayDateTime = new \DateTime($breakEventDay);

        $breakEvent->setBreakEventDay($breakEventDayDateTime);

        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $em->getRepository('AppBundle\Entity\Subscription');

        $subscriptionsWithClash = $subscriptionRepository->getClashingSubscriptions($breakEvent->getBreakEventDay());

        /** @var ArrayCollection $resultArray */
        $resultArray = new ArrayCollection();

        /** @var Subscription $clashingSub */
        foreach ($subscriptionsWithClash as $clashingSub) {
            if($clashingSub->getNumberOfExtensions() < 2) {
                // if the subscription has been extended zero or one time then do another +1 week extension

                /** @var \DateTime $subscriptionDueDate */
                $subscriptionDueDate = $clashingSub->getDueDate();
                /** @var \DateTime $oldDueDate */
                $oldDueDate = new \DateTime($subscriptionDueDate->format('Y-m-d H:i'));
                $subscriptionExtensionCount = $clashingSub->getNumberOfExtensions();


                $clashingSub->setDueDateTime($subscriptionDueDate->modify('+1 week'));
                $clashingSub->setNumberOfExtensions($subscriptionExtensionCount + 1);

                $resultArray->add(array(
                    'subscription' => $clashingSub,
                    'oldDueDate' => $oldDueDate->format('Y.m.d')
                ));
            } else if ($clashingSub->getNumberOfExtensions() >= 2) {

                $subscriptionExtensionCount = $clashingSub->getNumberOfExtensions();

                $clashingSub->setNumberOfExtensions($subscriptionExtensionCount + 1);
            }
        }

        $form = $this->createFormBuilder()
            ->add('save', 'submit', array(
                'attr' => array( 'class' => 'btn btn-success'),
                'label' => 'Mentés',
                'disabled' => true
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Subscription $extendedSubs */
            foreach ($subscriptionsWithClash as $extendedSubs) {
                $em->persist($extendedSubs);
            }
            $em->persist($breakEvent);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!' . PHP_EOL . 'Bérlet Hosszabbítások Elmentve!'
            );

            return $this->redirectToRoute('break_event_list_all');
        }

        return $this->render('subscription/listAllSubscriptions.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'subscriptions' => $resultArray,
                'form' => $form->createView(),
                'break_event' => $breakEvent,
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * Check if the break event with passed $id clashes with active period of any subscription.
     * Revert the extended due dates in the database if the Save form is valind and sent.
     *
     * @Route("/breakevent/check_and_revert_subs/{id}", name="breakevent_check_and_revert_subscriptions", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function checkAndRevertSubscriptionAndBreakEventsAction($id, Request $request) {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var BreakEventRepository $breakEventRepo */
        $breakEventRepo = $em->getRepository('AppBundle\Entity\BreakEvent');

        /** @var BreakEvent $breakEvent */
        $breakEvent = $breakEventRepo->find($id);

        if (!$breakEvent) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú szünet esemény: ' . $id . '!'
            );
            return $this->redirectToRoute('break_event_list_all');
        }

        /** @var SubscriptionRepository $subscriptionRepository */
        $subscriptionRepository = $em->getRepository('AppBundle\Entity\Subscription');

        $subscriptionsWithClash = $subscriptionRepository->getClashingSubscriptions($breakEvent->getBreakEventDay());

        /** @var ArrayCollection $resultArray */
        $resultArray = new ArrayCollection();

        /** @var Subscription $clashingSub */
        foreach ($subscriptionsWithClash as $clashingSub) {

            if($clashingSub->getNumberOfExtensions() <= 0) {
                // do nothing
            } else if($clashingSub->getNumberOfExtensions() <= 2) {
                // if the subscription has been extended zero or one time then do another -1 week extension

                /** @var \DateTime $subscriptionDueDate */
                $subscriptionDueDate = $clashingSub->getDueDate();
                /** @var \DateTime $oldDueDate */
                $oldDueDate = new \DateTime($subscriptionDueDate->format('Y-m-d H:i'));
                $subscriptionExtensionCount = $clashingSub->getNumberOfExtensions();


                $clashingSub->setDueDateTime($subscriptionDueDate->modify('-1 week'));
                $clashingSub->setNumberOfExtensions($subscriptionExtensionCount - 1);

                $resultArray->add(array(
                    'subscription' => $clashingSub,
                    'oldDueDate' => $oldDueDate->format('Y.m.d')
                ));
            } else {

                $subscriptionExtensionCount = $clashingSub->getNumberOfExtensions();

                $clashingSub->setNumberOfExtensions($subscriptionExtensionCount - 1);
            }
        }

        $form = $this->createFormBuilder()
            ->add('save', 'submit', array(
                'attr' => array(
                    'class' => 'btn btn-danger',
                ),
                'label' => 'Mentés és Szünet Törlése',
                'disabled' => true
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Subscription $revertedSubs */
            foreach ($subscriptionsWithClash as $revertedSubs) {
                $em->persist($revertedSubs);
            }

            $em->remove($breakEvent);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!' . PHP_EOL . 'Bérlet Hosszabbítások Visszaállítva!'
            );

            return $this->redirectToRoute('break_event_list_all');
        }

        return $this->render('subscription/listAllSubscriptions.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'subscriptions' => $resultArray,
                'form' => $form->createView(),
                'reverting' => true,
                'break_event' => $breakEvent,
                'logged_in_user' => $loggedInUser
            ));
    }
}