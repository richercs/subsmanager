<?php

namespace AppBundle\Entity;

use AppBundle\Repository\AttendanceHistoryRepository;
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

    public function __construct()
    {

    }

    /**
     * @var int
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
    protected $attendanceCount;

    /**
     * @var UserAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     */
    protected $owner;

    /**
     * @var UserAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="buyer_id", referencedColumnName="id", nullable=false)
     */
    protected $buyer;

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
     * @return string
     */
    public function __toString()
    {
        // TODO: Get rid of getStatus to short this string (it is used for select)

        return ' [' . $this->getId() . '] ' . $this->getOwner()->getUsername()
        . ' {' . $this->getStatusString() . '}'
        . ' (' . $this->getStartDateString() .' -'
        . ' ' . $this->getDueDateString() . ')';
    }

    /**
     * @return mixed
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
     * @return \DateTime
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
     * @param mixed $startDateTime
     */
    public function setStartDateTime($startDateTime)
    {
        // WILL be saved in the database
        $this->startDate = new \DateTime($startDateTime->format('Y-m-d H:i:s'));
    }

    /**
     * @return string
     */
    public function getStartDateString() {
        return $this->getStartDate()->format('Y.m.d. H:i');
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
     * @param mixed $dueDateTime
     */
    public function setDueDateTime($dueDateTime)
    {
        // WILL be saved in the database
        $this->dueDate = new \DateTime($dueDateTime->format('Y-m-d H:i:s'));
    }

    /**
     * @return string
     */
    public function getDueDateString() {
        return $this->getDueDate()->format('Y.m.d. H:i');
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
     * @return UserAccount
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * @param mixed $buyer
     */
    public function setBuyer($buyer)
    {
        $this->buyer = $buyer;
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
     * @return string
     */
    public function getStatusString()
    {
        $status = '';

        $now = new \DateTime();

        if($this->getDueDate() <$now) {
            $status = $status . 'LEJÁRT';
        }

        if(empty($status)) {
            $status = 'AKTÍV';
        }

        return $status;
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdated(new \DateTime('now'));

        if ($this->getCreated() == null) {
            $this->setCreated(new \DateTime('now'));
        }
    }
}
