<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountType extends AbstractType
{
//    /**
//     * @var UserAccount
//     */
//    protected $loggedInUser;
//
//    /**
//     * Constructor
//     *
//     * @param UserAccount $loggedInUser
//     */
//    public function __construct(UserAccount $loggedInUser)
//    {
//        $this->loggedInUser = $loggedInUser;
//    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_user_account';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', 'text')
            ->add('last_name', 'text')
            ->add('email', 'text')
            ->add('save', 'submit', array('label' => 'Save User Account'));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\UserAccount'
        ));
    }
}
