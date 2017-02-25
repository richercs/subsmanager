<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SessionEventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sessionEventDate', DateType::class, array(
                'widget' => 'single_text',
                'attr' => array( 'class' => 'datetimepicker'),
                'format' => 'yyyy-MM-dd HH:mm',
                'html5' => false,
            ))
            ->add('scheduleItem')
            ->add(
                'attendees', 'collection', array(
                    'entry_type' => AttendanceHistoryType::class,
                    'label' => 'Attendees',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required'     => false,
                    'attr' => array(
                        'class' => 'attendance-record',
                    ),
                    'by_reference' => false,
                )
            )
            ->add('sessionFeeNumbersSold')
            ->add('sessionFeeRevenueSold')
            ->add('save', 'submit', array(
                'label' => 'Save Session Event'
            ))
            ->add('delete','submit', array(
                'attr'      => array('class' => 'button-link delete'),
                'label'     => 'Delete'
            ));
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
