<?php

namespace AppBundle\Entity;

use AppBundle\Repository\AttendanceHistoryRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubscriptionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Subscription
{
	const SUBSCRIPTION_TYPE_ATTENDANCE = 'attendance';
	const SUBSCRIPTION_TYPE_CREDIT = 'credit';

	/**
	 * @var int $id
	 *
	 * @ORM\Id
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(name="date_start_date", type="datetime", nullable = false)
	 */
	protected $startDate;

	/**
	 * @ORM\Column(name="date_due_date", type="datetime", nullable = false)
	 */
	protected $dueDate;

	/**
	 * @ORM\Column(name="extensions_count", type="integer", nullable = false)
	 */
	protected $numberOfExtensions = 0;

	/**
	 * @ORM\Column(name="attendance_count", type="integer", nullable = true)
	 */
	protected $attendanceCount = null;

	/**
	 * @var int $credit
	 *
	 * @ORM\Column(name="credit", type="integer", nullable = true)
     * @Assert\NotBlank
     * @Assert\GreaterThan(value = 0)
	 */
	protected $credit;

	/**
	 * @ORM\Column(name="subscription_type", type="string", columnDefinition="ENUM('attendance', 'credit')")
	 */
	protected $subscriptionType;

	/**
	 * @var UserAccount
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
	 */
	protected $owner;

	/**
	 * @var int
	 * @ORM\Column(name="price", type="integer")
	 */
	protected $price;

	/**
	 * @ORM\Column(name="date_updated", type="datetime", nullable = true)
	 */
	protected $updated;

	/**
	 * @ORM\Column(name="date_created", type="datetime")
	 */
	protected $created;

	/**
	 * @var int
	 */
	protected $usages;

	/**
	 * @var int
	 */
	protected $currentCredit;

	/**
	 * @return string
	 */
	public function __toString()
	{
		return ' [' . $this->getId() . '] ' . $this->getOwner()->getUsername()
			. ' {' . $this->getStatusString() . '}'
			. ' (' . $this->getStartDateString() . ' -'
			. ' ' . $this->getDueDateString() . ')';
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return UserAccount
	 */
	public function getOwner()
	{
		return $this->owner;
	}

	/**
	 * @param UserAccount $owner
	 */
	public function setOwner($owner)
	{
		$this->owner = $owner;
	}

	/**
	 * @return string
	 */
	public function getStatusString()
	{
		return (!empty($this->getDueDate()) && $this->getDueDate() < new DateTime()) ? 'LEJÁRT' : 'AKTÍV';
	}

	/**
	 * @return mixed
	 */
	public function getDueDate()
	{
		return $this->dueDate;
	}

	/**
	 * @param mixed $dueDate
	 */
	public function setDueDate($dueDate)
	{
		$this->dueDate = $dueDate;
	}

	/**
	 * @return string
	 */
	public function getStartDateString()
	{
		return $this->getStartDate()->format('Y.m.d.');
	}

	/**
	 * @return DateTime
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @param mixed $startDate
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}

	/**
	 * @return string
	 */
	public function getDueDateString()
	{
		return $this->getDueDate()->format('Y.m.d.');
	}

	/**
	 * @param mixed $startDateTime
	 */
	public function setStartDateTime($startDateTime)
	{
		// WILL be saved in the database
		$this->startDate = new DateTime($startDateTime->format('Y-m-d H:i:s'));
	}

	/**
	 * @param mixed $dueDateTime
	 */
	public function setDueDateTime($dueDateTime)
	{
		// WILL be saved in the database
		$this->dueDate = new DateTime($dueDateTime->format('Y-m-d H:i:s'));
	}

	/**
	 * @return mixed
	 */
	public function getNumberOfExtensions()
	{
		return $this->numberOfExtensions;
	}

	/**
	 * @param mixed $numberOfExtensions
	 */
	public function setNumberOfExtensions($numberOfExtensions)
	{
		$this->numberOfExtensions = $numberOfExtensions;
	}

	/**
	 * @return mixed
	 */
	public function getAttendanceCount()
	{
		return $this->attendanceCount;
	}

	/**
	 * @param mixed $attendanceCount
	 */
	public function setAttendanceCount($attendanceCount)
	{
		$this->attendanceCount = $attendanceCount;
	}

	/**
	 * @return int
	 */
	public function getCredit()
	{
		return $this->credit;
	}

	/**
	 * @param int $credit
	 */
	public function setCredit($credit)
	{
		$this->credit = $credit;
	}

	/**
	 * @return mixed
	 */
	public function getSubscriptionType()
	{
		return $this->subscriptionType;
	}

	/**
	 * @param mixed $subscriptionType
	 */
	public function setSubscriptionType($subscriptionType)
	{
		if (!in_array($subscriptionType, [self::SUBSCRIPTION_TYPE_ATTENDANCE, self::SUBSCRIPTION_TYPE_CREDIT])) {
			throw new \InvalidArgumentException("Invalid subscription type");
		}
		$this->subscriptionType = $subscriptionType;
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @param mixed $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}

	/**
	 * @return mixed
	 */
	public function getUsages()
	{
		return $this->usages;
	}

	/**
	 * @param mixed $usages
	 */
	public function setUsages($usages)
	{
		$this->usages = $usages;
	}

	/**
	 * @return int
	 */
	public function getCurrentCredit()
	{
		return $this->currentCredit;
	}

	/**
	 * @param int $currentCredit
	 */
	public function setCurrentCredit($currentCredit)
	{
		$this->currentCredit = $currentCredit;
	}

	/**
	 * @return boolean
	 */
	public function getStatusBoolean()
	{
		$now = new DateTime();

		if ($this->getDueDate() < $now) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @return mixed
	 */
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @param mixed $updated
	 */
	public function setUpdated($updated)
	{
		$this->updated = $updated;
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param mixed $created
	 */
	public function setCreated($created)
	{
		$this->created = $created;
	}

	/**
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 */
	public function updatedTimestamps()
	{
		$this->setUpdated(new DateTime('now'));

		if ($this->getCreated() == null) {
			$this->setCreated(new DateTime('now'));
		}
	}
}
