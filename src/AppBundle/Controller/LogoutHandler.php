<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LogoutHandler
 *
 * @package AppBundle\Controller
 */
class LogoutHandler implements LogoutSuccessHandlerInterface
{
    /**
     * Url to redirect to after successful logout.
     *
     * @var string
     */
    private $redirectUrl;

    /**
     * @param $redirectUrl
     */
    public function __construct($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Creates a Response object to send upon a successful logout.
     *
     * @param Request $request
     *
     * @return Response never null
     */
    public function onLogoutSuccess(Request $request)
    {
        return new RedirectResponse($this->redirectUrl);
    }
}