<?php

namespace AppBundle\Form;

use AppBundle\Entity\UserAccount;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
     * @var array
     */
    protected $userCollection;

    /**
     * @var boolean
     */
    protected $isHandleContacts;

    /**
     * Constructor.
     *
     * @param UserAccount $loggedInUser
     */
    public function __construct(UserAccount $loggedInUser, array $userCollection = array(), $isHandleContacts = false) {
        $this->loggedInUser = $loggedInUser;
        $this->userCollection = $userCollection;
        $this->isHandleContacts = $isHandleContacts;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notAdminUser = !$this->loggedInUser->getIsAdmin();
        if($notAdminUser) {
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
            if(empty($this->userCollection) && !$this->isHandleContacts) {
                // Add new user account or edit an exisitng one as admin
                $builder
                    ->add('first_name')
                    ->add('last_name')
                    ->add('email', 'email', array(
                        'required' => false
                    ))
                    ->add('username')
                    ->add('change_password', 'submit', array(
                        'label' => 'Change password'
                    ))
                    ->add('save', 'submit', array(
                        'label' => 'Save User Account'
                    ))
                    ->add('delete','submit', array(
                        'attr'      => array('class' => 'button-link delete'),
                        'label'     => 'Delete'
                    ))
                ;
            } else {
                // Add new user account or register login credentials based on user contact as admin
                $builder
                    ->add('source_user_account_id', ChoiceType::class, array(
                        'label' => 'Non-enabled Users:',
                        'mapped' => false,
                        'choices' => $this->userCollection,
                        'data' => null,
                        'placeholder' => 'Choose an option',
                        'required' => false,
                    ))
                    ->add('first_name')
                    ->add('last_name')
                    ->add('email', 'email', array(
                        'required' => false
                    ))
                    ->add('username')
                    ->add('change_password', 'submit', array(
                        'label' => 'Change password'
                    ))
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
