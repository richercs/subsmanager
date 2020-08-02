<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\SessionSignUp;
use AppBundle\Entity\UserAccount;
use AppBundle\Repository\AnnouncedSessionRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

        $response = new JsonResponse();

        return $response->setData(array(
            'announcedSessionsData' => array(
                array(
                    'id' => 8,
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
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

        if (empty((int) $id)) {
            return new Response(null);
        }

        try {
            // TODO: extras POST paraméter kéne legyen
            $this->get('sign_up_manager')->signUpUserToSession(
                $loggedInUser,
                $id
            );

            // Successful session signup for logged in User
            $response = new JsonResponse();

            return $response->setData(array(
                "status" => "successful",
                "message" => null,
            ));

        } catch (\Exception $e) {
            $response = new JsonResponse();

            return $response->setData(array(
                "status" => "error",
                "message" => $e->getMessage(),
            ));
        }
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
        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

        $response = new JsonResponse();

        return $response->setData(array(
            "status" => "successful",
            "error" => null,
        ));
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
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

        if (empty((int) $id)) {
            return new Response(null);
        }

        try {

            $this->get('sign_up_manager')->signOffUserFromSession(
                $loggedInUser,
                $id
            );

            // Successful session signup for logged in User
            $response = new JsonResponse();

            return $response->setData(array(
                "status" => "successful",
                "message" => null,
            ));

        } catch (\Exception $e) {
            $response = new JsonResponse();

            return $response->setData(array(
                "status" => "error",
                "message" => $e->getMessage(),
            ));
        }
    }

}