<?php
/**
 * Created by PhpStorm.
 * User: csabi
 * Date: 1/27/17
 * Time: 6:42 PM
 */

namespace AppBundle\Controller;


use AppBundle\Entity\SessionEvent;
use AppBundle\Form\SessionEventType;
use AppBundle\Repository\UserAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SessionEventController extends Controller
{
    /**
     * @Route("/session_add_event", name="session_add_event")
     *
     * @param Request request
     * @return array
     */
    public function addSessionEventAction(Request $request)
    {
//        /** @var UserAccount $loggedInUser */
//        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $new_event = new SessionEvent();

        $form = $this->createForm(new SessionEventType(), $new_event);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($new_event);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('session_add_event');
        }

        return $this->render('event/addSessionEvent.html.twig',
            array(
                'new_event' => $new_event,
                'form' => $form->createView()
            ));

    }



}