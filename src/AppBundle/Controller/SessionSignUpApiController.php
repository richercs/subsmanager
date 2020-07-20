<?php


namespace AppBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SessionSignUpApiController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{

    /**
     * @Route("/api/sessiondata", name="sessiondata_get")
     *
     *
     * @param Request request
     * @return Response
     */
    public function getAnnouncedSessionDataAction (Request $request)
    {

        $response = new JsonResponse();

        return $response->setData(array(
            'announcedSessionsData' => array(
                array(
                    'id' => 1,
                    'sessionName' => "Szerda este kondi",
                    'timeOfEvent' => "2020-05-30 16:00",
                    'alreadySignedUp' => false,
                    'alreadyOnWaitList' => false,
                    'canSignUp' => true,
                    'canSignUpOnWaitList' => false,
                    'isListFinalized' => false,
                ),
                array(
                    'id' => 2,
                    'sessionName' => "Szerda este pilates",
                    'timeOfEvent' => "2020-05-30 18:00",
                    'alreadySignedUp' => false,
                    'alreadyOnWaitList' => false,
                    'canSignUp' => true,
                    'canSignUpOnWaitList' => false,
                    'isListFinalized' => false,
                ),
            ),
            'error' => null
        ));
    }

    /**
     * @Route("/api/do_signup/{id}", name="do_signup")
     *
     *
     * @param Request request
     * @return Response
     */
    public function doSignUpAction ($id, Request $request)
    {
        return null;
    }

    /**
     * @Route("/api/do_signup_on_waitlist/{id}", name="do_signup_on_waitlist")
     *
     *
     * @param Request request
     * @return Response
     */
    public function doSignUpOnWaitListAction ($id, Request $request)
    {
        return null;
    }

    /**
     * @Route("/api/do_signoff/{id}", name="do_signoff")
     *
     *
     * @param Request request
     * @return Response
     */
    public function doSignOffAction ($id, Request $request)
    {
        return null;
    }

}