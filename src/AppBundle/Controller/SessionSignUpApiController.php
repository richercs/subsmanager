<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\SessionSignUp;
use AppBundle\Entity\UserAccount;
use AppBundle\Repository\AnnouncedSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @param Request $request
     * @return Response
     */
    public function getAnnouncedSessionDataAction (Request $request)
    {

        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

        /**
         * If there is no available session then return null
         * Available session: Announced session where session event id is null
         */
        $availableSessions = $this->get('sign_up_manager')->getAvailableSessions();

        if (empty($availableSessions)) {
            return new Response(null);
        }

        $announcedSessionDataCollection = new ArrayCollection();

        /** @var AnnouncedSession $availableSession */
        foreach ($availableSessions as $availableSession) {
            try {

                // This is necessary to check because if user is not signed up then we can't check for any
                // property of session sign up by user like extras
                $alreadySignedUp = $this->get('sign_up_manager')->isUserSignedUpToSession($loggedInUser, $availableSession->getId());

                $announcedSessionDataCollection->add(array(
                    'id' => $availableSession->getId(),
                    'sessionName' => $availableSession->getScheduleItem()->getSessionName(),
                    'timeOfEvent' => $availableSession->getTimeOfEvent()->format('Y.m.d. H:i:s'),
                    'alreadySignedUp' => $this->get('sign_up_manager')->isUserSignedUpToSession($loggedInUser, $availableSession->getId()),
                    'alreadyOnWaitList' => $this->get('sign_up_manager')->isUserWaitListedToSession($loggedInUser, $availableSession->getId()),
                    'canSignUp' => $this->get('sign_up_manager')->userCanSignUpToSession($loggedInUser, $availableSession->getId()),
                    'canSignUpOnWaitList' => $this->get('sign_up_manager')->userCanSignUpToWaitList($loggedInUser, $availableSession->getId()),
                    'isListFinalized' => $availableSession->isFinalized(),
                    'extras' => $alreadySignedUp ? $this->get('sign_up_manager')->getExtrasSetByUser($loggedInUser, $availableSession->getId()) : null
                ));
            } catch (\Exception $e) {
                continue;
            }
        }

        $response = new JsonResponse();

        return $response->setData(array(
            'announcedSessionsData' => $announcedSessionDataCollection->toArray(),
            'error' => null
        ));
    }

    /**
     * @Route("/api/do_signup/{id}", name="do_signup")
     *
     * @param integer $id
     * @param Request $request
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
     * @param integer $id
     * @param Request $request
     * @return Response
     */
    public function doSignUpOnWaitListAction ($id, Request $request)
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
            $this->get('sign_up_manager')->waitListUserToSession(
                $loggedInUser,
                $id
            );

            // Successful wait listed session signup for logged in User
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
     * @Route("/api/do_signoff/{id}", name="do_signoff")
     *
     * @param integer $id
     * @param Request $request
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

            // Successful session sign off for logged in User
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
     * @Route("/loadAnnouncedSessionInfo", name="load_announced_session_info")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request $request
     * @return Response
     */
    public function loadAnnouncedSessionInfo(Request $request)
    {
        $announcedSessionId = $request->get('announced_session_id');

        if(empty($announcedSessionId)) {
            return new Response(null);
        }

        /** @var AnnouncedSessionRepository $announcedSessionRepository */
        $announcedSessionRepository = $this->get('announced_session_repository');

        /** @var AnnouncedSession $announcedSession */
        $announcedSession = $announcedSessionRepository->find($announcedSessionId);

        if(empty($announcedSession)) {
            return new Response(null);
        }

        $sessionSignees = new ArrayCollection();

        /** @var SessionSignUp $signee */
        foreach ($announcedSession->getSignees() as $signee) {

            $sessionSignees->add(array(
                'announced_session_id' => $signee->getAnnouncedSession()->getId(),
                'signee_name' => $signee->getSignee()->getUsername(),
                'extras' => $signee->getExtras(),
                'is_wait_listed' => $signee->isWaitListed()
            ));
        }

        $response = new JsonResponse();

        return $response->setData(array(
            'id' => $announcedSession->getId(),
            'signees' => $sessionSignees->toArray(),
        ));
    }

    /**
     * @Route("/setExtrasForSignedUpUser", name="set_extras_to_session_signee")
     *
     *
     * @param Request $request
     * @return Response
     */
    public function setExtrasForSignedUpUser(Request $request)
    {

        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

        $extrasValue = $request->get('extras_value');

        $announcedSessionId = $request->get('announced_session_id');

        if($announcedSessionId === null || $extrasValue === null) {
            return new Response(null);
        }

        try {

            $this->get('sign_up_manager')->setExtrasForSignee(
                $loggedInUser,
                $extrasValue,
                $announcedSessionId
            );

            // Successful session sign off for logged in User
            $response = new JsonResponse();

            return $response->setData(array(
                "status" => "successful",
                "message" => "Sikeresen elmentve!",
                "session_id" => $announcedSessionId
            ));

        } catch (\Exception $e) {
            $response = new JsonResponse();

            return $response->setData(array(
                "status" => "error",
                "message" => $e->getMessage(),
                "session_id" => $announcedSessionId
            ));
        }

    }

}