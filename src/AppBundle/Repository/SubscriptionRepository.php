<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BreakEvent;
use Doctrine\ORM\EntityRepository;


class SubscriptionRepository extends EntityRepository
{
	public function getRunningSubs()
	{
	}

	/**
	 *
	 * @param BreakEvent $breakEvent
	 * @return array
	 */
	public function getClashingSubscriptions($break_day)
	{
		$query = $this->_em->createQuery('
                SELECT subscription
                FROM AppBundle\Entity\Subscription subscription
                WHERE subscription.startDate <= :break_day
                AND subscription.dueDate >= :break_day
           ');

		$query->setParameters(array(
			'break_day' => $break_day,
		));

		$result = $query->getResult();

		return $result;
	}

	public function getSubscriptionsBetweenDates($stats_start, $stats_due)
	{
		$query = $this->_em->createQuery('
                SELECT subscription
                FROM AppBundle\Entity\Subscription subscription
                WHERE subscription.startDate >= :stats_start
                AND subscription.startDate <= :stats_due
           ');

		$query->setParameters(array(
			'stats_start' => $stats_start,
			'stats_due' => $stats_due
		));

		$result = $query->getResult();

		return $result;
	}

	public function getLastFiftySubscriptions()
	{
		$query = $this->_em->createQuery('
                SELECT subscription
                FROM AppBundle\Entity\Subscription subscription
                ORDER BY subscription.id DESC
           ');

		$query->setMaxResults(50);

		$result = $query->getResult();

		return $result;
	}

	public function findUsableSubscriptions()
	{
		$query = $this->_em->createQuery('
            SELECT
              s.id, COALESCE(SUM(session_event.sessionCreditRequirement), 0) as c, COALESCE(s.credit, 0) AS treshold
            FROM
                AppBundle\Entity\Subscription s
            LEFT JOIN
              AppBundle\Entity\AttendanceHistory ah WITH ah.subscription = s.id
            LEFT JOIN
              AppBundle\Entity\SessionEvent session_event WITH ah.session_event = session_event.id
            WHERE
              s.dueDate >= CURRENT_DATE()
            GROUP BY
              s.id
            HAVING
              c < treshold
        ');

		$runningSubs = $query->getArrayResult();

		$runningSubIds = array_column($runningSubs, 'id');

		return $this->findBy(array('id' => $runningSubIds));
	}

	public function findUsableSubscriptionsForUser($owner)
	{
		$query = $this->_em->createQuery('
            SELECT
              s.id, COUNT(ah.id) as c, COALESCE(s.attendanceCount, 0) AS treshold
            FROM
                AppBundle\Entity\Subscription s
            LEFT JOIN
              AppBundle\Entity\AttendanceHistory ah WITH ah.subscription = s.id
            WHERE
              s.dueDate >= CURRENT_DATE()
              AND
              s.owner = :owner
            GROUP BY
              s.id
            HAVING
              c < treshold
        ');

		$query->setParameters(array(
			'owner' => $owner
		));

		$runningSubs = $query->getArrayResult();

		$runningSubIds = array_column($runningSubs, 'id');

		return $this->findBy(array('id' => $runningSubIds));
	}
}
