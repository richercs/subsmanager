<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserContact
 *
 * @ORM\Table(name="user_contact")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserContactRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserContact
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
     * @var string
     *
     * @ORM\Column(name="contact_first_name", type="string", length=140)
     * @Assert\NotBlank()
     */
    protected $contact_first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_last_name", type="string", length=140)
     * @Assert\NotBlank()
     */
    protected $contact_last_name;

    /**
     * @var string
     * @ORM\Column(name="contact_email", type="string", length=140)
     * @Assert\NotBlank()
     */
    protected $contact_email;

    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    protected $created;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $password;

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContactFirstName()
        . ' ' . $this->getContactLastName()
        . ' ' . $this->getContactEmail()
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
     * @return string
     */
    public function getContactFirstName()
    {
        return $this->contact_first_name;
    }

    /**
     * @param string $contact_first_name
     */
    public function setContactFirstName($contact_first_name)
    {
        $this->contact_first_name = $contact_first_name;
    }

    /**
     * @return string
     */
    public function getContactLastName()
    {
        return $this->contact_last_name;
    }

    /**
     * @param string $contact_last_name
     */
    public function setContactLastName($contact_last_name)
    {
        $this->contact_last_name = $contact_last_name;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contact_email;
    }

    /**
     * @param string $contact_email
     */
    public function setContactEmail($contact_email)
    {
        $this->contact_email = $contact_email;
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
     * @return UserContact
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
     * @return UserContact
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