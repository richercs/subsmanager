<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\UserContactType;
use AppBundle\Entity\UserContact;
use AppBundle\Repository\UserContactRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class UserContactController extends Controller
{
    /**
     * @Route("/usercontact/list_all", name="usercontact_list_all")
     *
     * @param Request request
     * @return array
     */
    public function listAllUserContactsAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserContactRepository $userc_repo */
        $userc_repo = $em->getRepository('AppBundle\Entity\UserContact');

        $contacts = $userc_repo->findAll();

        return $this->render('contacts/listAllUserContacts.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'contacts' => $contacts
            ));
    }

    /**
     * @Route("usercontact/add_usercontact", name="usercontact_add_contact")
     *
     * @param Request request
     * @return array
     */
    public function addUserContactAction(Request $request)
    {
//        /** @var UserAccount $loggedInUser */
//        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserContactRepository $userContactRepository */
        $userContactRepository = $em->getRepository('AppBundle\Entity\UserContact');

        $new_contact = new UserContact();

        $form = $this->createForm(new UserContactType(), $new_contact);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em->persist($new_contact);
            $em->flush();
            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('usercontact_add_contact');
        }

        return $this->render('contacts/addUserContact.html.twig',
            array(
                'new_contact' => $new_contact,
                'form' => $form->createView()
            ));
    }
}