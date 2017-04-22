<?php

namespace AppBundle\Form;

use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SubscriptionType extends AbstractType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'app_subscription';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('owner', AutocompleteType::class, array(
                'class' => 'AppBundle:UserAccount'
            ))
            ->add('buyer',  AutocompleteType::class, array(
                'class' => 'AppBundle:UserAccount'
            ))
            ->add('isMonthlyTicket')
            ->add('attendanceCount')
            ->add('start_date', DateType::class, array(
                'widget' => 'single_text',
                'attr' => array( 'class' => 'datetimepicker'),
                'format' => 'yyyy-MM-dd HH:mm',
                'html5' => false,
            ))
            ->add('price', 'money', array(
                'currency' => 'HUF'
            ))
            ->add('save', 'submit', array(
                'label' => 'Mentés'
            ))
            ->add('delete','submit', array(
                'attr'      => array('class' => 'button-link delete'),
                'label'     => 'Törlés'
            ));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Subscription'
        ));
    }
}
