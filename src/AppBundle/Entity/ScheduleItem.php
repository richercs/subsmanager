<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ScheduleItem
 *
 * @ORM\Table(name="schedule_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ScheduleItemRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ScheduleItem
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="item_name", nullable = false)
     */
    protected $scheduledItemName;

    /**
     * @var string
     *
     * @ORM\Column(name="scheduled_day", nullable = false)
     */
    protected $scheduledDay;

    /**
     * @var string
     *
     * @ORM\Column(name="scheduled_start_time", nullable = false)
     */
    protected $scheduledStartTime;

    /**
     * @var string
     *
     * @ORM\Column(name="scheduled_due_time", nullable = false)
     */
    protected $scheduledDueTime;

    /**
     * @var string
     *
     * @ORM\Column(name="location", length=140, nullable = false)
     */
    protected $location;

    /**
     * @var string
     *
     * @ORM\Column(name="session_name", length=140, nullable = true)
     */
    protected $session_name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", nullable=false)
     */
    protected $isActive;

    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $created;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getScheduledItemName()
        . ' {(' . $this->getScheduledDay() .')'
        . ' (' . $this->getScheduledStartTime() .')'
        . ' (' . $this->getScheduledDueTime() .')}'
        . ' ' . $this->getLocation()
        . ' [' . $this->getId() . ']';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getScheduledItemName()
    {
        return $this->scheduledItemName;
    }

    /**
     * @param mixed $scheduledItemName
     */
    public function setScheduledItemName($scheduledItemName)
    {
        $this->scheduledItemName = $scheduledItemName;
    }

    /**
     * @return string
     */
    public function getScheduledDay()
    {
        return $this->scheduledDay;
    }

    /**
     * @param string $scheduledDay
     */
    public function setScheduledDay($scheduledDay)
    {
        $this->scheduledDay = $scheduledDay;
    }

    /**
     * @return string
     */
    public function getScheduledStartTime()
    {
        return $this->scheduledStartTime;
    }

    /**
     * @param string $scheduledStartTime
     */
    public function setScheduledStartTime($scheduledStartTime)
    {
        $this->scheduledStartTime = $scheduledStartTime;
    }

    /**
     * @return string
     */
    public function getScheduledDueTime()
    {
        return $this->scheduledDueTime;
    }

    /**
     * @param string $scheduledDueTime
     */
    public function setScheduledDueTime($scheduledDueTime)
    {
        $this->scheduledDueTime = $scheduledDueTime;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getSessionName()
    {
        return $this->session_name;
    }

    /**
     * @param mixed $session_name
     */
    public function setSessionName($session_name)
    {
        $this->session_name = $session_name;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
     * @return ScheduleItem
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
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
     * @return ScheduleItem
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
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