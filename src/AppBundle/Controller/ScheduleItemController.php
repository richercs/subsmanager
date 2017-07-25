<?php


namespace AppBundle\Controller;


use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\UserAccount;
use AppBundle\Form\ScheduleItemType;
use AppBundle\Repository\ScheduleItemRepository;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ScheduleItemController extends Controller
{
    /**
     * @Route("/schedule/list_all", name="schedule_list_all")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function listAllScheduleItemsAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var ScheduleItemRepository $scheduleItemRepo */
        $scheduleItemRepo = $em->getRepository('AppBundle\Entity\ScheduleItem');

        $items = $scheduleItemRepo->findAll();

        /** ArrayCollection activeItems */
        $activeItems = new ArrayCollection();

        /** ArrayCollection inactiveItems */
        $inactiveItems = new ArrayCollection();

        /** @var ScheduleItem $item */
        foreach ($items as $item) {

            if($item->isDeleted()) {
                $inactiveItems->add($item);
            } else {
                $activeItems->add($item);
            }
        }

        return $this->render('schedule/listAllItems.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'active_items' => $activeItems,
                'inactive_items' => $inactiveItems,
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("/schedule/add_item", name="schedule_add_item")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addScheduleItemAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $newItem = new ScheduleItem();

        $form = $this->createForm(new ScheduleItemType(), $newItem);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($newItem);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('schedule_add_item');
        }

        return $this->render('schedule/addItem.html.twig',
            array(
                'new_item' => $newItem,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));

    }

    /**
     * Opens edit page for schedule items with passed $id.
     *
     * @Route("/schedule/{id}", name="schedule_edit_item", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function editScheduleItemAction($id, Request $request) {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var ScheduleItemRepository $scheduleRepository */
        $scheduleItemRepository = $em->getRepository('AppBundle\Entity\ScheduleItem');

        // Editing schedule item
        /** @var ScheduleItem $scheduleItem */
        $scheduleItem = $scheduleItemRepository->find($id);

        if (!$scheduleItem) {
            $this->addFlash(
                'error',
                'Nincs ilyen azonosítójú órarendi elem: ' . $id . '!'
            );
            return $this->redirectToRoute('schedule_list_all');
        }

        $form = $this->createForm(new ScheduleItemType(), $scheduleItem);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // DELETE Schedule item
            if ($form->get('delete')->isClicked()) {
                $em->remove($scheduleItem);
                $em->flush();

                // message
                $this->addFlash(
                    'notice',
                    '"' . $id . '" azonosítójú órarendi elem sikeresen törölve!'
                );

                // show list
                return $this->redirectToRoute('schedule_list_all');
            }

            $em->persist($scheduleItem);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('schedule_list_all');
        }

        return $this->render('schedule/editItem.html.twig',
            array(
                'schedule_item' => $scheduleItem,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }


}