<?php

namespace SubsmanagerUserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends BaseController
{
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        /** @var Request $req */
        $req = $this->container->get('request_stack')->getCurrentRequest();

        $template = $req->get('embeded', false) ? '@FOSUser/Security/login-embeded.html.twig' : '@FOSUser/Security/login.html.twig';

        return $this->render($template, $data);
    }
}
