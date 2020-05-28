<?php


namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

}