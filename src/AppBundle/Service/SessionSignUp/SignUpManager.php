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
	/**
	 * @var AnnouncedSessionRepository
	 */
	private $announcedSessionRepository;

	/**
	 * @var SessionSignUpsRepository
	 */
	private $sessionSignUpsRepository;

	/**
	 * @param AnnouncedSessionRepository $announcedSessionRepository
	 * @param SessionSignUpsRepository $sessionSignUpsRepository
	 */
	public function __construct(
		AnnouncedSessionRepository $announcedSessionRepository,
		SessionSignUpsRepository   $sessionSignUpsRepository
	)
	{
		$this->announcedSessionRepository = $announcedSessionRepository;
		$this->sessionSignUpsRepository = $sessionSignUpsRepository;
	}

	/**
	 * Get all available Announced Sessions
	 *
	 * @return array
	 */
	public function getAvailableSessions()
	{
		$availableSingleLimitedSessions = $this->announcedSessionRepository->getAvailableSingleLimitedSessionsOrderedByTimeOfEvent();
		$availableWeeklyOnlineSessions= $this->announcedSessionRepository->getAvailableWeeklyOnlineSessionsOrderedByTimeOfEvent();

		return array_merge($availableSingleLimitedSessions, $availableWeeklyOnlineSessions);
	}

	/**
	 * Check if user is already signed up to a session
	 *
	 * @param UserAccount $loggedInUser
	 * @param int $id
	 * @return bool
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
	 *
	 * @param UserAccount $loggedInUser
	 * @param int $id
	 * @return bool
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
	 *
	 * @param UserAccount $loggedInUser
	 * @param int $id
	 * @return bool
	 * @throws Exception
	 */
	public function userCanSignUpToSession(UserAccount $loggedInUser, $id)
	{
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

		$now = new DateTime('now');

		if (($announcedSession->getTimeOfSignUpStart()->getTimestamp() <= $now->getTimestamp())
			&& ($announcedSession->getTimeFromFinalized()->getTimestamp() >= $now->getTimestamp())
		) {
			return true;
		}
		return false;
	}

	/**
	 * Check if the user can sign up to a wait list of a session
	 *
	 * @param UserAccount $loggedInUser
	 * @param int $id
	 * @return bool
	 * @throws Exception
	 */
	public function userCanSignUpToWaitList(UserAccount $loggedInUser, $id)
	{
		/** @var AnnouncedSession $announcedSession */
		$announcedSession = $this->announcedSessionRepository->find($id);

		if (!$announcedSession) {
			throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
		}

		if ($announcedSession->getAnnouncedSessionType() !== AnnouncedSession::ANNOUNCED_SESSION_TYPE_SINGLE_LIMITED) {
			return false;
		}

		// Check if user is already signed up to the session
		if ($this->isUserSignedUpToSession($loggedInUser, $id)) {
			return false;
		}

		// Check if user is already wait listed to the session
		if ($this->isUserWaitListedToSession($loggedInUser, $id)) {
			return false;
		}

		// Check if user can sign up to a session
		if ($this->userCanSignUpToSession($loggedInUser, $id)) {
			return false;
		}

		$now = new DateTime('now');

		if (($announcedSession->getTimeOfSignUpStart()->getTimestamp() <= $now->getTimestamp())
			&& ($announcedSession->getTimeFromFinalized()->getTimestamp() >= $now->getTimestamp())
		) {
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
		/** @var AnnouncedSession $announcedSession */
		$announcedSession = $this->announcedSessionRepository->find($id);

		if (!$announcedSession) {
			throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
		}

		if ($announcedSession->isFinalized() || $announcedSession->isFull()) {
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
		/** @var AnnouncedSession $announcedSession */
		$announcedSession = $this->announcedSessionRepository->find($id);

		if (!$announcedSession) {
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
		/** @var AnnouncedSession $announcedSession */
		$announcedSession = $this->announcedSessionRepository->find($id);

		if (!$announcedSession) {
			throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
		}

		if ($announcedSession->isFinalized()) {
			throw new Exception('Validációs hiba: Az óra véglegesítve van!');
		}

		$arrayOfsignee = $this->sessionSignUpsRepository->findBy(['announcedSession' => $announcedSession, 'signee' => $loggedInUser]);

		if (!isset($arrayOfsignee[0])) {
			throw new Exception('Validációs hiba: Nincs ilyen bejelentkezés: '
				. 'felhasználó: ' . $loggedInUser->getUsername()
				. ' bejelentkezéses óra azonosító: ' . $announcedSession->getId());
		}

		/** @var SessionSignUp $signee */
		$signee = $arrayOfsignee[0];

		$announcedSession->removeSignee($signee);

		$this->announcedSessionRepository->save($announcedSession);

		$this->sessionSignUpsRepository->delete($signee);
	}

	/**
	 * Get the current number of extras set in database for user
	 *
	 * @param UserAccount $loggedInUser
	 * @param int $id
	 * @return int
	 * @throws Exception
	 */
	public function getExtrasSetByUser(UserAccount $loggedInUser, $id)
	{
		/** @var AnnouncedSession $announcedSession */
		$announcedSession = $this->announcedSessionRepository->find($id);

		if (!$announcedSession) {
			throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $id . '!');
		}

		$arrayOfsignee = $this->sessionSignUpsRepository->findBy(['announcedSession' => $announcedSession, 'signee' => $loggedInUser]);

		if (!isset($arrayOfsignee[0])) {
			throw new Exception('Validációs hiba: Nincs ilyen bejelentkezés: '
				. 'felhasználó: ' . $loggedInUser->getUsername()
				. ' bejelentkezéses óra azonosító: ' . $announcedSession->getId());
		}

		/** @var SessionSignUp $signee */
		$signee = $arrayOfsignee[0];

		return $signee->getExtras();
	}


	/**
	 * @param UserAccount $loggedInUser
	 * @param integer $extras
	 * @param integer $announcedSessionId
	 * @throws Exception
	 */
	public function setExtrasForSignee(UserAccount $loggedInUser, $extras, $announcedSessionId)
	{
		/** @var AnnouncedSession $announcedSession */
		$announcedSession = $this->announcedSessionRepository->find($announcedSessionId);

		if (!$announcedSession) {
			throw new Exception('Nincs ilyen azonosítójú bejelentkezéses óra: ' . $announcedSessionId . '!');
		}

		if ($announcedSession->isFinalized()) {
			throw new Exception('Validációs hiba: Az óra véglegesítve van!');
		}

		$arrayOfsignee = $this->sessionSignUpsRepository->findBy(['announcedSession' => $announcedSession, 'signee' => $loggedInUser]);

		if (!isset($arrayOfsignee[0])) {
			throw new Exception('Validációs hiba: Nincs ilyen bejelentkezés: '
				. 'felhasználó: ' . $loggedInUser->getLastName() . ' ' . $loggedInUser->getFirstName()
				. ' bejelentkezéses óra azonosító: ' . $announcedSession->getId());
		}

		/** @var SessionSignUp $signee */
		$signee = $arrayOfsignee[0];

		// Validate for business rules

		$maxNumberOfSignees = $announcedSession->getMaxNumberOfSignUps();

		$announcedSession->calculateNumberOfSignees();

		$countOfSignees = $announcedSession->getNumberOfSignees();

		$incrementOfSigneesByNewExtras = ($signee->getExtras() > $extras) ? 0 : $extras - $signee->getExtras();

		if ($extras !== "0") {
			if ($incrementOfSigneesByNewExtras > 0 && $announcedSession->isFull()) {
				throw new Exception('Az óra megtelt, így ezt az értéket nem tudod elmenteni!');
			} elseif ($countOfSignees + $incrementOfSigneesByNewExtras > $maxNumberOfSignees) {
				throw new Exception('Ezt az értéket nem tudod elmenteni, mert az óra túllépné a maximális résztvevők számát!');
			}
		}

		// save changes to database
		$signee->setExtras($extras);

		$this->sessionSignUpsRepository->save($signee);
	}
}
