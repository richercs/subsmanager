<?php

namespace AppBundle\Repository;

use AppBundle\Entity\SessionData;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class SessionDataRepository extends EntityRepository
{
    public function getRunningSessionDatas() {
        $return = new ArrayCollection();

        $sessionData1 = new SessionData();
        $sessionData1->setCreated(new \DateTime());

        $return->add($sessionData1);

        return $return;
    }

}