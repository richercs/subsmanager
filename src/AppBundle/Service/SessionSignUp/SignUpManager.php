<?php


namespace AppBundle\Service\SessionSignUp;

use AppBundle\Entity\SessionSignUp;
use AppBundle\Entity\AnnouncedSession;
use AppBundle\Entity\UserAccount;
use AppBundle\Repository\SessionSignUpsRepository;
use AppBundle\Repository\AnnouncedSessionRepository;
use AppBundle\Repository\SubscriptionRepository;
use DateInterval;
use DateTime;
use Exception;

/**
 * Service class to manage Session signup states
 */
class SignUpManager
{

    private $announcedSessionRepository;

    private $sessionSignUpsRepository;

    private $subscriptionRepository;

    /**
     * @param AnnouncedSessionRepository $announcedSessionRepository
     * @param SessionSignUpsRepository $sessionSignUpsRepository
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(AnnouncedSessionRepository $announcedSessionRepository,
                                SessionSignUpsRepository $sessionSignUpsRepository,
                                SubscriptionRepository $subscriptionRepository)
    {
        $this->announcedSessionRepository = $announcedSessionRepository;
        $this->sessionSignUpsRepository = $sessionSignUpsRepository;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Get all available Announced Sessions
     *
     * @return array
     */
    public function getAvailableSessions()
    {
        return $this->announcedSessionRepository->findBy(['sessionEvent' => null]);
    }

    /**
     * Check if user is already signed up to a session
     * @param UserAccount $loggedInUser
     * @param int $id
     * @throws Exception
     */
    public function isUserSignedUpToSession(UserAccount $loggedInUser, $id)
    {
        /** @var AnnouncedSession $announcedSession */
        $announcedSession = $this->announcedSessionRepository->find($id);

        if (!$announcedSession) {
            throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
        }

        /** @var SessionSignUp $signee */
        foreach ($announcedSession->getSignees() as $signee) {
            if ($signee->getSignee() === $loggedInUser && !$signee->isWaitListed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is already wait listed to a session
     * @param UserAccount $loggedInUser
     * @param int $id
     * @throws Exception
     */
    public function isUserWaitListedToSession(UserAccount $loggedInUser, $id)
    {
        /** @var AnnouncedSession $announcedSession */
        $announcedSession = $this->announcedSessionRepository->find($id);

        if (!$announcedSession) {
            throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
        }

        /** @var SessionSignUp $signee */
        foreach ($announcedSession->getSignees() as $signee) {
            if ($signee->getSignee() === $loggedInUser && $signee->isWaitListed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user can sign up to a session
     * @param UserAccount $loggedInUser
     * @param int $id
     * @throws Exception
     */
    public function userCanSignUpToSession(UserAccount $loggedInUser,  $id) {

        /** @var AnnouncedSession $announcedSession */
        $announcedSession = $this->announcedSessionRepository->find($id);

        if (!$announcedSession) {
            throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
        }

        // Check if user is already signed up to the session
        if ($this->isUserSignedUpToSession($loggedInUser, $id)) {
            return false;
        }

        // Check if user is already wait listed to the session
        if ($this->isUserWaitListedToSession($loggedInUser, $id)) {
            return false;
        }

        // Check if the announced session is already full - this means user can only be wait listed
        if ($announcedSession->isFull()) {
            return false;
        }

        // Check if the announced session already has at least one wait listed signee
        if ($announcedSession->hasWaitlistedSignee()) {
            return false;
        }

        /**
         * Based on if the user has an active subscription with available usages
         * the time the user can start signing up is earlier at -1 day 0 hours 0 minutes
         * if the user has no active subscription or any available usages left
         * the time the user can start signing up is later at -1 day 16 hours 0 minutes
         */
        $timeOfFinalization = $announcedSession->getTimeFromFinalized()->format('D M d Y H:i:s e');

        $timeOfAvailabilityByUserSubs = new DateTime($timeOfFinalization);

        $timeOfAvailabilityByUserSubs->sub(new DateInterval('P1D'));

        if(!empty($this->subscriptionRepository->findUsableSubscriptionsForUser($loggedInUser))) {
            $timeOfAvailabilityByUserSubs->setTime(0,0,0);
        } else {
            $timeOfAvailabilityByUserSubs->setTime(16,0,0);
        }

        $now = new DateTime('now');

        // Check if the current time is between the time of availability adjusted by user subscriptions status
        // and the time the announces session is finalized
        if (($timeOfAvailabilityByUserSubs->getTimestamp() <= $now->getTimestamp())
            && ($announcedSession->getTimeFromFinalized()->getTimestamp() >= $now->getTimestamp())) {

            return true;
        }
        return false;
    }

    /**
     * Check if the user can sign up to a wait list of a session
     * @param UserAccount $loggedInUser
     * @param int $id
     * @throws Exception
     */
    public function userCanSignUpToWaitList(UserAccount $loggedInUser,  $id) {

        /** @var AnnouncedSession $announcedSession */
        $announcedSession = $this->announcedSessionRepository->find($id);

        if (!$announcedSession) {
            throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
        }

        // Check if user is already signed up to the session
        if ($this->isUserSignedUpToSession($loggedInUser, $id)) {
            return false;
        }

        // Check if user is already wait listed to the session
        if ($this->isUserWaitListedToSession($loggedInUser, $id)) {
            return false;
        }

        // Check if user can sign ut to a session
        if ($this->userCanSignUpToSession($loggedInUser, $id)) {
            return false;
        }

        $timeOfFinalization = $announcedSession->getTimeFromFinalized()->format('D M d Y H:i:s e');

        $timeOfAvailabilityByUserSubs = new DateTime($timeOfFinalization);

        $timeOfAvailabilityByUserSubs->sub(new DateInterval('P1D'));

        if(!empty($this->subscriptionRepository->findUsableSubscriptionsForUser($loggedInUser))) {
            $timeOfAvailabilityByUserSubs->setTime(0,0,0);
        } else {
            $timeOfAvailabilityByUserSubs->setTime(16,0,0);
        }

        $now = new DateTime('now');

        // Check if the current time is between the time of availability adjusted by user subscriptions status
        // and the time the announces session is finalized
        if (($timeOfAvailabilityByUserSubs->getTimestamp() <= $now->getTimestamp())
            && ($announcedSession->getTimeFromFinalized()->getTimestamp() >= $now->getTimestamp())) {

            return true;
        }
        return false;
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

        $sessionSignUp = new SessionSignUp();

        $sessionSignUp->setAnnouncedSession($announcedSession);
        $sessionSignUp->setSignee($loggedInUser);
        $sessionSignUp->setExtras($numberOfExtras);
        $sessionSignUp->setWaitListed(false);

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

        $sessionSignUpOnWaitList = new SessionSignUp();

        $sessionSignUpOnWaitList->setAnnouncedSession($announcedSession);
        $sessionSignUpOnWaitList->setSignee($loggedInUser);
        $sessionSignUpOnWaitList->setExtras($numberOfExtras);
        $sessionSignUpOnWaitList->setWaitListed(true);

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