<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionEventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sessionEventDate')
            ->add('scheduleItem')
            ->add(
                'attendees', 'entity', array(
                    'class' => 'AppBundle\Entity\AttendanceHistory',
                    'property' => 'attendee',
                    'multiple' => TRUE,
                    'expanded' => TRUE,
                    'label' => 'Attendees',
                )
            )
            ->add('sessionFeeNumbersSold')
            ->add('sessionFeeRevenueSold')
            ->add('save', 'submit', array('label' => 'Save Session Event'));
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SessionEvent'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_sessionevent';
    }


}
