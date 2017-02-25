<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Subscription;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;


class SubscriptionRepository extends EntityRepository
{
    public function getRunningSubs() {
    }
}
