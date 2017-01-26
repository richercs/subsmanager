<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Subscription;
use AppBundle\Form\SubscriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SubscriptionController extends Controller
{
    /**
     * @Route("/subscription_add_subscription", name="subscription_add_subscription")
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
            return $this->redirectToRoute('subscription_add_subscription');
        }

        return $this->render('subscription/addSubscription.html.twig',
            array(
                'new_subscription' => $new_subscription,
                'form' => $form->createView()
            ));

    }

}