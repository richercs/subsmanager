<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(name="time_of_event", type="datetime", nullable = false)
     */
    protected $timeOfEvent;

    /**
     * @ORM\Column(name="time_from_finalized", type="datetime", nullable = false)
     */
    protected $timeFromFinalized;

    /**
     * @var int
     * @ORM\Column(name="max_number_of_signups", type="integer", nullable = false)
     */
    protected $maxNumberOfSignUps;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\SessionSignUp", mappedBy="announcedSession", cascade={"remove","persist"})
     */
    protected $signees;

    // TODO: Decide if this will every be needed, if not remove it
    /**
     * @var ArrayCollection $signeesOnWaitList
     */
    protected $signeesOnWaitList;

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
            $this->numberOfSignees = $this->numberOfSignees + 1 +$numberOfExtras;
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getSigneesOnWaitList()
    {
        return $this->signeesOnWaitList;
    }

    /**
     * @param ArrayCollection $signeesOnWaitList
     */
    public function setSigneesOnWaitList($signeesOnWaitList)
    {
        $this->signeesOnWaitList = $signeesOnWaitList;
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
}