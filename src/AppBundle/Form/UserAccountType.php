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
                    'label' => 'Jelszó Változtatás'
                ))
                ->add('save', 'submit', array(
                    'label' => 'Mentés'
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
                    ->add('username','text', array(
                        'label' => 'Felhasználónév'
                    ))
                    ->add('change_password', 'submit', array(
                        'label' => 'Jelszó Változtatás'
                    ))
                    ->add('save', 'submit', array(
                        'label' => 'Mentés'
                    ))
                    ->add('delete','submit', array(
                        'attr'      => array('class' => 'button-link delete'),
                        'label'     => 'Törlés'
                    ))
                ;
            } else {
                // Add new user account or register login credentials based on user contact as admin
                $builder
                    ->add('source_user_account_id', ChoiceType::class, array(
                        'label' => 'Felhasználók',
                        'mapped' => false,
                        'choices' => $this->userCollection,
                        'data' => null,
                        'placeholder' => 'Válasz egy felhasználót',
                        'required' => false,
                    ))
                    ->add('first_name')
                    ->add('last_name')
                    ->add('email', 'email', array(
                        'required' => false
                    ))
                    ->add('username')
                    ->add('change_password', 'submit', array(
                        'label' => 'Jelszó Változtatás'
                    ))
                    ->add('save', 'submit', array(
                        'label' => 'Mentés'
                    ))
                    ->add('delete','submit', array(
                        'attr'      => array('class' => 'button-link delete'),
                        'label'     => 'Törlés'
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
