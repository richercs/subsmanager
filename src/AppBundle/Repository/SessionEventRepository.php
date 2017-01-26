<?php

namespace AppBundle\Repository;

use AppBundle\Entity\SessionEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class SessionEventRepository extends EntityRepository
{
    public function getRunningSessionEvents() {
        $return = new ArrayCollection();

        $event1 = new SessionEvent();
        $event1->setCreated(new \DateTime());

        $return->add($event1);

        return $return;
    }

}