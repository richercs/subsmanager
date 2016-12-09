<?php

namespace AppBundle\Repository;

use AppBundle\Entity\TicketData;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class TicketDataRepository extends EntityRepository
{
    public function getRunningTicketDatas() {
        $return = new ArrayCollection();

        $ticket1 = new TicketData();
        $ticket1->setCreated(new \DateTime());

        $return->add($ticket1);

        return $return;
    }

}