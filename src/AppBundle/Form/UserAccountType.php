<?php

namespace AppBundle\Form;

use AppBundle\Entity\UserAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAccountType extends AbstractType
{
    /**
     * @var UserAccount
     */
    protected $loggedInUser;

    /**
     * Constructor.
     *
     * @param UserAccount $loggedInUser
     */
    public function __construct(UserAccount $loggedInUser) {
        $this->loggedInUser = $loggedInUser;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bAdmin = !$this->loggedInUser->getIsAdmin();
        if($bAdmin) {
            $builder
                ->add('first_name')
                ->add('last_name')
                ->add('email', 'email', array(
                    'required' => false
                ))
                ->add('change_password', 'submit', array(
                    'label' => 'Change password'
                ))
                ->add('save', 'submit', array(
                    'label' => 'Save User Account'
                ))
            ;
        } else {
            $builder
                ->add('first_name')
                ->add('last_name')
                ->add('email', 'email', array(
                    'required' => false
                ))
                ->add('username')
                ->add('save', 'submit', array(
                    'label' => 'Save User Account'
                ))
                ->add('delete','submit', array(
                    'attr'      => array('class' => 'button-link delete'),
                    'label'     => 'Delete'
                ))
            ;
        }

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\UserAccount'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_useraccount';
    }


}
