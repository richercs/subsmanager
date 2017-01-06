<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SessionData
 *
 * @ORM\Table(name="session_data")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SessionDataRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SessionData
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    // TODO: Óratípus?

    /**
     * @ORM\Column(name="scheduled_date", type="datetime", nullable = true)
     */
    protected $scheduledDate;

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
    public function getScheduledDate()
    {
        return $this->scheduledDate;
    }

    /**
     * @param mixed $scheduledDate
     */
    public function setScheduledDate($scheduledDate)
    {
        $this->scheduledDate = $scheduledDate;
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
     * @return SessionData
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
     * @return SessionData
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