<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\UserAccount;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;


class AnnouncedSessionController extends Controller
{
    /**
     * @Route("announced_session/search_edit_announced_session", name="announced_session_search_edit")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function searchAnnouncedSessionForEditAction(Request $request)
    {

    }

    /**
     * @Route("/announced_session/add_announced_session", name="add_announced_session")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function addAnnouncedSessionAction(Request $request)
    {

    }

    /**
     * @Route("/announced_session/{id}", name="edit_announced_session", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function editAnnouncedSessionAction($id, Request $request)
    {

    }
}