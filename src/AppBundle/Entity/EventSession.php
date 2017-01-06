<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EventSession
 *
 * @ORM\Table(name="event_session")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventSessionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class EventSession
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
     * @var SessionData
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SessionData")
     * @ORM\JoinColumn(name="session_data_id", referencedColumnName="id")
     */
    protected $sessionData;

    /**
     * @ORM\Column(name="actual_date", type="datetime", nullable = true)
     */
    protected $actualDate;

    /**
     * @var string
     *
     * @ORM\Column(name="location", length=140)
     */
    protected $location;

    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $created;

    // TODO toString functions

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
     * @return SessionData
     */
    public function getSessionData()
    {
        return $this->sessionData;
    }

    /**
     * @param SessionData $sessionData
     */
    public function setSessionData($sessionData)
    {
        $this->sessionData = $sessionData;
    }

    /**
     * @return mixed
     */
    public function getActualDate()
    {
        return $this->actualDate;
    }

    /**
     * @param mixed $actualDate
     */
    public function setActualDate($actualDate)
    {
        $this->actualDate = $actualDate;
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
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     * @return EventSession
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
     * @return EventSession
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