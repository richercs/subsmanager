<?php

namespace AppBundle\Repository;

use AppBundle\Entity\EventSession;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class EventSessionRepository extends EntityRepository
{
    public function getRunningSessions() {
        $return = new ArrayCollection();

        $session1 = new EventSession();
        $session1->setCreated(new \DateTime());

        $return->add($session1);

        return $return;
    }

}