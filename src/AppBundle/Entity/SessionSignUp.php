<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SessionSignUp
 *
 * @ORM\Table(name="session_signups")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SessionSignUpsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SessionSignUp
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
     * @var AnnouncedSession
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AnnouncedSession", inversedBy="signees")
     * @ORM\JoinColumn(name="announced_session_id", referencedColumnName="id")
     */
    protected $announcedSession;

    /**
     * @var UserAccount
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccount")
     * @ORM\JoinColumn(name="signee_id", referencedColumnName="id")
     */
    protected $signee;

    /**
     * @var int
     * @ORM\Column(name="number_of_extras", type="integer", nullable = true)
     */
    protected $extras;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_waitlisted", type="boolean")
     *
     */
    protected $waitListed = 0;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

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
     * @return AnnouncedSession
     */
    public function getAnnouncedSession()
    {
        return $this->announcedSession;
    }

    /**
     * @param AnnouncedSession $announcedSession
     */
    public function setAnnouncedSession($announcedSession)
    {
        $this->announcedSession = $announcedSession;
    }

    /**
     * @return UserAccount
     */
    public function getSignee()
    {
        return $this->signee;
    }

    /**
     * @param UserAccount $signee
     */
    public function setSignee($signee)
    {
        $this->signee = $signee;
    }

    /**
     * @return int
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param int $extras
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;
    }

    /**
     * @return bool
     */
    public function isWaitListed()
    {
        return $this->waitListed;
    }

    /**
     * @param bool $waitListed
     */
    public function setWaitListed($waitListed)
    {
        $this->waitListed = $waitListed;
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
     * @return SessionSignUp
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
     * @return SessionSignUp
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