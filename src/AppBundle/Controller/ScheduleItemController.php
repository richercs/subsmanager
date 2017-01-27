<?php


namespace AppBundle\Controller;


use AppBundle\Entity\ScheduleItem;
use AppBundle\Form\ScheduleItemType;
use AppBundle\Repository\UserAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ScheduleItemController extends Controller
{
    /**
     * @Route("/schedule_add_item", name="schedule_add_item")
     *
     * @param Request request
     * @return array
     */
    public function addScheduleItemAction(Request $request)
    {
//        /** @var UserAccount $loggedInUser */
//        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $new_item = new ScheduleItem();

        $form = $this->createForm(new ScheduleItemType(), $new_item);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($new_item);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('schedule_add_item');
        }

        return $this->render('schedule/addItem.html.twig',
            array(
                'new_item' => $new_item,
                'form' => $form->createView()
            ));

    }


}