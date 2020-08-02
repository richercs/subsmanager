<?php


namespace AppBundle\Service\SessionSignUp;

use AppBundle\Entity\SessionSignUp;
use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\UserAccount;
use AppBundle\Repository\SessionSignUpsRepository;
use AppBundle\Repository\AnnouncedSessionRepository;
use Exception;

/**
 * Service class to manage Session signup states
 */
class SignUpManager
{

    private $announcedSessionRepository;

    private $sessionSignUpsRepository;

    /**
     * @param AnnouncedSessionRepository $announcedSessionRepository
     * @param SessionSignUpsRepository $sessionSignUpsRepository
     */
    public function __construct(AnnouncedSessionRepository $announcedSessionRepository, SessionSignUpsRepository $sessionSignUpsRepository)
    {
        $this->announcedSessionRepository = $announcedSessionRepository;
        $this->sessionSignUpsRepository = $sessionSignUpsRepository;
    }

    /**
     * @param UserAccount $loggedInUser
     * @param int $id
     * @param int $numberOfExtras
     * @throws Exception
     */
    public function signUpUserToSession(UserAccount $loggedInUser, $id, $numberOfExtras = 0)
    {
        $announcedSession = $this->announcedSessionRepository->find($id);

        if(!$announcedSession) {
            throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
        }

        if ($announcedSession->isFinalized() || $announcedSession->isFull() || $announcedSession->hasWaitlistedSignee()) {
            throw new Exception('Validációs hiba miatt nem sikerült a bejelentkezés!');
        }

        /** @var SessionSignUp $signee */
        foreach ($announcedSession->getSignees() as $signee) {
            if ($signee->getSignee() === $loggedInUser) {
                throw new Exception('Validációs hiba: A bejelentkező már szerepel ezen az órán!');
            }
        }

        $sessionSignUp = new SessionSignUp($announcedSession, $loggedInUser, $numberOfExtras, 0);

        $announcedSession->addSignee($sessionSignUp);

        $this->announcedSessionRepository->save($announcedSession);
    }

    /**
     * @param UserAccount $loggedInUser
     * @param int $id
     * @param int $numberOfExtras
     * @throws Exception
     */
    public function waitListUserToSession(UserAccount $loggedInUser, $id, $numberOfExtras = 0)
    {
        $announcedSession = $this->announcedSessionRepository->find($id);

        if(!$announcedSession) {
            throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
        }

        if ($announcedSession->isFinalized()) {
            throw new Exception('Validációs hiba: Az óra véglegesítve van!');
        }

        /** @var SessionSignUp $signee */
        foreach ($announcedSession->getSignees() as $signee) {
            if ($signee->getSignee() === $loggedInUser) {
                throw new Exception('Validációs hiba: A bejelentkező már szerepel ezen az órán!');
            }
        }

        $sessionSignUpOnWaitList = new SessionSignUp($announcedSession, $loggedInUser, $numberOfExtras, 1);

        $announcedSession->addSignee($sessionSignUpOnWaitList);

        $this->announcedSessionRepository->save($announcedSession);
    }

    /**
     * @param UserAccount $loggedInUser
     * @param int $id
     * @throws Exception
     */
    public function signOffUserFromSession(UserAccount $loggedInUser, $id)
    {
        $announcedSession = $this->announcedSessionRepository->find($id);

        if(!$announcedSession) {
            throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
        }

        if ($announcedSession->isFinalized()) {
            throw new Exception('Validációs hiba: Az óra véglegesítve van!');
        }

        $arrayOfsignee =  $this->sessionSignUpsRepository->findBy(['announcedSession' => $announcedSession, 'signee' => $loggedInUser]);

        if (!isset($arrayOfsignee[0])) {
            throw new Exception('Validációs hiba: Nincs ilyen bejelentkezés: '
                . 'felhasználó: ' . $loggedInUser->getUsername()
                . ' bejelentkezéses óra azonosító: ' .$announcedSession->getId());
        }

        /** @var SessionSignUp $signee */
        $signee = $arrayOfsignee[0];

        $announcedSession->removeSignee($signee);

        $this->announcedSessionRepository->save($announcedSession);

        $this->sessionSignUpsRepository->delete($signee);
    }

}