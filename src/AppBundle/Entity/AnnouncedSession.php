<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AnnouncedSession
 *
 * @ORM\Table(name="announced_session")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AnnouncedSessionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AnnouncedSession
{
	const ANNOUNCED_SESSION_TYPE_SINGLE_LIMITED = 'single_limited';
	const ANNOUNCED_SESSION_TYPE_WEEKLY_ONLINE = 'weekly_online_unlimited';

    public function __construct() {
        $this->signees = new ArrayCollection();
        $this->signeesOnWaitList = new ArrayCollection();
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
     * @var ScheduleItem
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ScheduleItem")
     * @ORM\JoinColumn(name="schedule_item_id", referencedColumnName="id", nullable=false)
     */
    protected $scheduleItem;

	/**
	 * @ORM\Column(name="announced_session_type", type="string", columnDefinition="ENUM('single_limited', 'weekly_online_unlimited')")
	 */
	protected $announcedSessionType;

    /**
     * @var SessionEvent
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\SessionEvent", inversedBy="announcedSession")
     * @ORM\JoinColumn(name="session_event_id", referencedColumnName="id", nullable=true)
     */
    protected $sessionEvent;

    /**
     * @ORM\Column(name="time_of_event", type="datetime", nullable = false)
     */
    protected $timeOfEvent;

    /**
     * @ORM\Column(name="time_from_finalized", type="datetime", nullable = false)
     */
    protected $timeFromFinalized;

    /**
     * @var int
     * @ORM\Column(name="max_number_of_signups", type="integer", nullable = true)
     */
    protected $maxNumberOfSignUps;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SessionSignUp", mappedBy="announcedSession", cascade={"remove","persist"})
     */
    protected $signees;

    /**
     * @var int $numberOfSignees
     */
    protected $numberOfSignees;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    public function addSignee(SessionSignUp $signee)
    {
        $signee->setAnnouncedSession($this);

        $this->signees->add($signee);
    }

    public function removeSignee(SessionSignUp $signee)
    {
        $this->signees->removeElement($signee);
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
     * @return ScheduleItem
     */
    public function getScheduleItem()
    {
        return $this->scheduleItem;
    }

    /**
     * @param ScheduleItem $scheduleItem
     */
    public function setScheduleItem($scheduleItem)
    {
        $this->scheduleItem = $scheduleItem;
    }

	/**
	 * @return mixed
	 */
	public function getAnnouncedSessionType()
	{
		return $this->announcedSessionType;
	}

	/**
	 * @param mixed $announcedSessionType
	 */
	public function setAnnouncedSessionType($announcedSessionType)
	{
		$this->announcedSessionType = $announcedSessionType;
	}

    /**
     * @return SessionEvent
     */
    public function getSessionEvent()
    {
        return $this->sessionEvent;
    }

    /**
     * @param SessionEvent $sessionEvent
     */
    public function setSessionEvent($sessionEvent)
    {
        $this->sessionEvent = $sessionEvent;
    }

    /**
     * @return mixed
     */
    public function getTimeOfEvent()
    {
        return $this->timeOfEvent;
    }

    /**
     * @param mixed $timeOfEvent
     */
    public function setTimeOfEvent($timeOfEvent)
    {
        $this->timeOfEvent = $timeOfEvent;
    }

    /**
     * @return mixed
     */
    public function getTimeFromFinalized()
    {
        return $this->timeFromFinalized;
    }

    /**
     * @param mixed $timeFromFinalized
     */
    public function setTimeFromFinalized($timeFromFinalized)
    {
        $this->timeFromFinalized = $timeFromFinalized;
    }

    /**
     * @return int
     */
    public function getMaxNumberOfSignUps()
    {
        return $this->maxNumberOfSignUps;
    }

    /**
     * @param int $maxNumberOfSignUps
     */
    public function setMaxNumberOfSignUps($maxNumberOfSignUps)
    {
        $this->maxNumberOfSignUps = $maxNumberOfSignUps;
    }

    /**
     * @return ArrayCollection
     */
    public function getSignees()
    {
        return $this->signees;
    }

    /**
     * @param ArrayCollection $signees
     */
    public function setSignees($signees)
    {
        $this->signees = $signees;
    }

    /**
     * @return int
     */
    public function getNumberOfSignees()
    {
        return $this->numberOfSignees;
    }

    /**
     * @param int $numberOfSignees
     */
    public function setNumberOfSignees($numberOfSignees)
    {
        $this->numberOfSignees = $numberOfSignees;
    }

    public function calculateNumberOfSignees()
    {
        $this->numberOfSignees = 0;

        /** @var SessionSignUp $signee*/
        foreach ($this->getSignees() as $signee) {
            $numberOfExtras = $signee->getExtras() ? : 0;
            $this->numberOfSignees = $this->numberOfSignees + 1 + $numberOfExtras;
        }
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return AnnouncedSession
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     * @return AnnouncedSession
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedAtTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    /**
     * @return boolean
     */
    public function isFinalized()
    {
        return $this->timeFromFinalized <= new \DateTime('now');
    }

    /**
     * @return boolean
     */
    public function isFull()
    {
        $this->calculateNumberOfSignees();

        return $this->numberOfSignees >= $this->maxNumberOfSignUps;
    }

    /**
     * @return boolean
     */
    public function hasWaitlistedSignee()
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('waitListed', true));

        return $this->signees->matching($criteria)->count() > 0;
    }

    /**
     * @return integer
     */
    public function getNumberOfWaitlistedSignees()
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('waitListed', true));

        return $this->signees->matching($criteria)->count();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '[' . $this->getId() . ']' . ' ' . $this->getScheduleItem() . ' ' . $this->getTimeOfEvent()->format('Y.m.d H:i:s');
    }


}
