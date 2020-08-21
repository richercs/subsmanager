<?php


namespace AppBundle\Repository;


use AppBundle\Entity\SessionSignUp;
use Doctrine\ORM\EntityRepository;

class SessionSignUpsRepository extends EntityRepository
{
    /**
     * Save the session sign up to database
     *
     * @param SessionSignUp $signee
     */
    public function save(SessionSignUp $signee)
    {
        $this->_em->persist($signee);
        $this->_em->flush();
    }

    /**
     * Remove the session sign up from database
     *
     * @param SessionSignUp $signee
     */
    public function delete(SessionSignUp $signee)
    {
        $this->_em->remove($signee);
        $this->_em->flush();
    }
}