<?php


namespace AppBundle\Controller;


use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\SessionSignUp;
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
        $loggedInUser = $this->getUser();

        if (!$loggedInUser) {
            return new Response(null);
        }

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var AnnouncedSessionRepository  $announcedSessionRepo */
        $announcedSessionRepo = $em->getRepository('AppBundle\Entity\AnnouncedSession');

        /** @var AnnouncedSession $announcedSession */
        $announcedSession = $announcedSessionRepo->find($id);

        if(!$announcedSession) {
            $response = new JsonResponse();

            return $response->setData(array(
                'status' => 'error',
                'message' => 'Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!'
            ));
        }

        // TODO: ez egy try catch kéne legyen

        /**
         * Check for Business logic on Announced Session
         * - Not finalized
         * - Not full
         * - Has no waitlisted signee
         * - Not Already Signed up
         */
        if ($announcedSession->isFinalized() || $announcedSession->isFull() || $announcedSession->hasWaitlistedSignee()) {

            $response = new JsonResponse();

            return $response->setData(array(
                'status' => 'error',
                'message' => 'Sikertelen bejelentkezés validációs hiba miatt!'
            ));
        }

        // TODO: extras POST paraméter kéne legyen

        /** @var SessionSignUp $sessionSignUp */
        $sessionSignUp = new SessionSignUp($announcedSession, $loggedInUser, 0, 0);

        $announcedSession->addSignee($sessionSignUp);

        $em->persist($announcedSession);
        $em->flush();

        $response = new JsonResponse();

        return $response->setData(array(
            "status" => "successful",
            "message" => null,
        ));
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

}