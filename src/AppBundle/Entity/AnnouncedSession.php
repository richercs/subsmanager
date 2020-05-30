<?php


namespace AppBundle\Entity;

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
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $created;

}