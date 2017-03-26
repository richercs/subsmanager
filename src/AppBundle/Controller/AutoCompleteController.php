<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use AppBundle\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AutoCompleteController extends Controller
{
    /**
     * @Route("/useraccount_search", name="useraccount_search")
     *
     * @param Request request
     * @return array
     */
    public function searchUserAccountAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        $term = $request->query->get('term'); // use "term" instead of "q" for jquery-ui

        $results = $userAccountRepository->findLikeUserName($term);

        return $this->render('event/attendeeSearch.twig', array(
            'results' => $results
        ));
    }

    /**
     * @Route("/useraccount_get/{id}", name="useraccount_get")
     *
     * @param Request request
     * @return Response
     */
    public function getUserAccountAction($id = null)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserAccountRepository $userAccountRepository */
        $userAccountRepository = $em->getRepository('AppBundle\Entity\UserAccount');

        /** @var UserAccount $useraccount */
        $useraccount = $userAccountRepository->find($id);

        return new Response($useraccount->getUsername());
    }


    /**
     * @Route("/load_subscription_record", name="load_subscription_record")
     *
     * @param Request request
     * @return Response
     */
    public function loadSubscriptionRecord(Request $request) {
        $ownerId = $request->get('owner_id');
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var Subscription $s */
        $s = $em->getRepository(Subscription::class)->findOneBy(array('owner' => $ownerId));

        $response = new JsonResponse();
        return $response->setData(array(
            'id' => 123,
            'label' => (string) $s,
            'owner' => $ownerId
        ));
    }
}