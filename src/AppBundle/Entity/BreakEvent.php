<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BreakEvent
 *
 * @ORM\Table(name="break_event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BreakEventRepository")
 * @ORM\HasLifecycleCallbacks
 */
class BreakEvent
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
     * @ORM\Column(name="break_event_day", type="datetime", nullable = false)
     */
    protected $breakEventDay;

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
        return $this->getBreakEventDay()->format('Y-m-d H:i')
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
    public function getBreakEventDay()
    {
        return $this->breakEventDay;
    }

    /**
     * @param mixed $breakEventDay
     */
    public function setBreakEventDay($breakEventDay)
    {
        $this->breakEventDay = $breakEventDay;
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
     * @return BreakEvent
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
     * @return BreakEvent
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