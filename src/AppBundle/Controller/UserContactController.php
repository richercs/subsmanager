<?php


namespace AppBundle\Controller;

use AppBundle\Entity\UserAccount;
use FOS\UserBundle\Util\PasswordUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\UserContactType;
use AppBundle\Entity\UserContact;
use AppBundle\Repository\UserContactRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserContactController extends Controller
{
    /**
     * @Route("/usercontact/list_all", name="usercontact_list_all")
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function listAllUserContactsAction(Request $request)
    {
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserContactRepository $userContactRepo */
        $userContactRepo = $em->getRepository('AppBundle\Entity\UserContact');

        $contacts = $userContactRepo->findAll();

        // Do not show deleted user contacts
        for( $i= 0 ; $i < count($contacts) ; $i++ )
        {
            /** @var UserContact $result */
            $result = $contacts[$i];
            if(!is_null($result->getDeletedAt())) {
                unset($contacts[$i]);
            }
        }

        return $this->render('contacts/listAllUserContacts.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'contacts' => $contacts,
                'logged_in_user' => $loggedInUser
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
        /** @var UserAccount $loggedInUser */
        $loggedInUser = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var PasswordUpdater $passwordHasher */
        $passwordHasher = $this->get('fos_user.util.password_updater');

        /** @var UserContactRepository $userContactRepository */
        $userContactRepository = $em->getRepository('AppBundle\Entity\UserContact');

        $newContact = new UserContact();

        $form = $this->createForm(new UserContactType(), $newContact);
        $form->handleRequest($request);

        if ($form->isValid())
        {

            $dummyUser = new UserAccount();
            $dummyUser->setPlainPassword($newContact->getPassword());

            $passwordHasher->hashPassword($dummyUser);

            $newContact->setPassword($dummyUser->getPassword());

            $em->persist($newContact);

            $em->flush();

            $this->addFlash(
                'notice',
                'Változtatások Elmentve!'
            );
            return $this->redirectToRoute('usercontact_add_contact');
        }

        return $this->render('contacts/addUserContact.html.twig',
            array(
                'new_contact' => $newContact,
                'form' => $form->createView(),
                'logged_in_user' => $loggedInUser
            ));
    }

    /**
     * @Route("usercontact/delete_usercontact/{id}", name="usercontact_delete_contact", defaults={"id" = -1})
     *
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @param Request request
     * @return array
     */
    public function deleteUserContactAction($id, Request $request) {

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var UserContactRepository $userContactRepository */
        $userContactRepository = $em->getRepository('AppBundle\Entity\UserContact');

        /** @var UserContact $user_contact */
        $userContact = $userContactRepository->find($id);

        // DELETE user contact
        $em->remove($userContact);
        $em->flush();

        // message
        $this->addFlash(
            'notice',
            '"' . $id . '" azonosítójú kapcsolat felvételi űrlap sikeresen törölve!'
        );

        // show home
        return $this->redirectToRoute('homepage');
    }
}