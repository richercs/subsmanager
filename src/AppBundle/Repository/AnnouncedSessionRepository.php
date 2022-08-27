<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AnnouncedSession;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;

class AnnouncedSessionRepository extends EntityRepository
{
	public function getAvailableSingleLimitedSessionsOrderedByTimeOfEvent()
	{
		$query = $this->createQueryBuilder('announced_session')
			->where('announced_session.announcedSessionType = :sessionType')
			->andWhere('announced_session.timeFromFinalized >= CURRENT_TIMESTAMP()')
			->orderBy('announced_session.timeOfEvent', 'ASC')
			->getQuery();

		$query->setParameters([
			'sessionType' => AnnouncedSession::ANNOUNCED_SESSION_TYPE_SINGLE_LIMITED,
		]);

		return $query->getResult();
	}

	public function getAvailableWeeklyOnlineSessionsOrderedByTimeOfEvent()
	{
		$query = $this->createQueryBuilder('announced_session')
			->where('announced_session.announcedSessionType = :sessionType')
			->andWhere('announced_session.timeFromFinalized >= CURRENT_TIMESTAMP()')
			->orderBy('announced_session.timeOfEvent', 'ASC')
			->getQuery();

		$query->setParameters([
			'sessionType' => AnnouncedSession::ANNOUNCED_SESSION_TYPE_WEEKLY_ONLINE,
		]);

		return $query->getResult();
	}

	public function getLastThirty()
	{
		$query = $this->_em->createQuery('
                SELECT announced_session
                FROM AppBundle\Entity\AnnouncedSession announced_session
                ORDER BY announced_session.createdAt DESC
           ');

		$query->setMaxResults(30);

		return $query->getResult();
	}

	public function getBetweenDates($stats_start, $stats_due)
	{
		$query = $this->_em->createQuery('
                SELECT announced_session
                FROM AppBundle\Entity\AnnouncedSession announced_session
                WHERE announced_session.createdAt >= :stats_start
                AND announced_session.createdAt <= :stats_due
                ORDER BY announced_session.createdAt DESC
           ');

		$query->setParameters(array(
			'stats_start' => $stats_start,
			'stats_due' => $stats_due
		));

		return $query->getResult();
	}

	public function getLastThirtyFilteredScheduleItem($statsScheduleItem)
	{
		$query = $this->_em->createQuery('
                SELECT announced_session
                FROM AppBundle\Entity\AnnouncedSession announced_session
                WHERE announced_session.scheduleItem = :filter
                ORDER BY announced_session.createdAt DESC
           ');

		$query->setParameters(array(
			'filter' => $statsScheduleItem
		));

		$query->setMaxResults(30);

		return $query->getResult();
	}

	public function getBetweenDatesFilteredScheduleItem($stats_start, $stats_due, $statsScheduleItem)
	{
		$query = $this->_em->createQuery('
                SELECT announced_session
                FROM AppBundle\Entity\AnnouncedSession announced_session
                WHERE announced_session.createdAt >= :stats_start
                AND announced_session.createdAt <= :stats_due
                AND announced_session.scheduleItem = :filter
           ');

		$query->setParameters(array(
			'stats_start' => $stats_start,
			'stats_due' => $stats_due,
			'filter' => $statsScheduleItem
		));

		return $query->getResult();
	}

	/**
	 * Save the announced session to the database
	 *
	 * @param AnnouncedSession $announcedSession
	 * @throws OptimisticLockException
	 */
	public function save(AnnouncedSession $announcedSession)
	{
		$this->_em->persist($announcedSession);
		$this->_em->flush();
	}
}
