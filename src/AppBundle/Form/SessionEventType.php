<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            // TODO: This needs to be a list of users mapped to the subscription they use to attend.
            // TODO: Both the user needs to be selected, then the subscription or session fee option.
                // TODO: Pending solution is complete.
            ->add(
                'attendees', 'collection', array(
                    'entry_type' => AttendanceHistoryType::class,
                    'label' => 'Attendees',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'attr' => array(
                        'class' => 'my-selector',
                    ),
                    'by_reference' => false,
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
