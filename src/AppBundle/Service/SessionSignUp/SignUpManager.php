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

}