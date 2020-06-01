<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\ScheduleItem;
use AppBundle\Entity\SessionSignUps;
use AppBundle\Form\AnnouncedSessionType;
use AppBundle\Repository\ScheduleItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;


class AnnouncedSessionController extends Controller
{
    /**
     * @Route("announced_session/search_edit_announced_session", name="announced_session_search_edit")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function searchAnnouncedSessionForEditAction(Request $request)
    {

    }

    /**
     * @Route("/announced_session/add_announced_session", name="add_announced_session")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addAnnouncedSessionAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var ScheduleItemRepository $scheduleItemRepository */
        $scheduleItemRepository = $em->getRepository(ScheduleItem::class);

        $scheduleItemCollection = $scheduleItemRepository->findAll();

        $scheduleItemCollection = array_combine(range(1, count($scheduleItemCollection)), array_values($scheduleItemCollection));

        /** @var ScheduleItem $scheduleItem */
        foreach ($scheduleItemCollection as $key => $scheduleItem) {
            if($scheduleItem->isDeleted()) {
                unset($scheduleItemCollection[$key]);
            }
        }

        $newAnnouncedSession = new AnnouncedSession();

        $form = $this->createForm(new AnnouncedSessionType($scheduleItemCollection, true), $newAnnouncedSession);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $scheduleItemId = $request->get('appbundle_announced_session')['scheduleItem'];

            $scheduleItem = $scheduleItemRepository->find($scheduleItemId);

            $newAnnouncedSession->setScheduleItem($scheduleItem);

            /** @var SessionSignUps $waitListedSignee */
            foreach ($newAnnouncedSession->getSigneesOnWaitList() as $waitListedSignee) {
                $newAnnouncedSession->addSigneeToWaitList($waitListedSignee);
            }

            // TODO: Validate by business rules if need be

            // The OneToMany association and the symfony-collection bundle manages the signups ArrayCollection
            $em->persist($newAnnouncedSession);
            $em->flush();
            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );


            return $this->redirectToRoute('add_announced_session');
        }

        return $this->render('signups/addAnnouncedSession.html.twig',
            array(
                'new_announced_session' => $newAnnouncedSession,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("/announced_session/{id}", name="edit_announced_session", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function editAnnouncedSessionAction($id, Request $request)
    {

    }
}