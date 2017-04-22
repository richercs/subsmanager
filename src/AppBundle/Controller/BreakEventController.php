<?php


namespace AppBundle\Controller;



use AppBundle\Entity\BreakEvent;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\BreakEventType;
use AppBundle\Repository\BreakEventRepository;
use AppBundle\Repository\UserAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
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
                'breaks' => $breaks,
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

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $newBreak = new BreakEvent();

        $form = $this->createForm(new BreakEventType(), $newBreak);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($newBreak);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('break_add_breakevent');
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

        $form = $this->createForm(new BreakEventType(), $breakEvent);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // DELETE Schedule item
            if ($form->get('delete')->isClicked()) {
                $em->remove($breakEvent);
                $em->flush();

                // message
                $this->addFlash(
                    'notice',
                    '"' . $id . '" azonosítójú szünet esemény sikeresen törölve!'
                );

                // show list
                return $this->redirectToRoute('break_event_list_all');
            }

            $em->persist($breakEvent);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('break_event_list_all');
        }

        return $this->render('break/editBreakEvent.html.twig',
            array(
                'break_event' => $breakEvent,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }


}