<?php


namespace AppBundle\Controller;


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
        
        return null;
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