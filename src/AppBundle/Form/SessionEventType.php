<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SessionEventType extends AbstractType
{

    /**
     * @var array
     */
    protected $announcedSessionCollection;

    /**
     * @var array
     */
    protected $scheduleItemCollection;

    /**
     * @var boolean
     */
    protected $onAddPage;

    /**
     * Constructor.
     *
     ** @param array $announcedSessionCollection
     ** @param array $scheduleItemCollection
     ** @param boolean $onAddPage
     */
    public function __construct($announcedSessionCollection = array(), $scheduleItemCollection = array(), $onAddPage = false)
    {
        $this->announcedSessionCollection = $announcedSessionCollection;
        $this->scheduleItemCollection = $scheduleItemCollection;
        $this->onAddPage = $onAddPage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($this->onAddPage) {
            $builder
                ->add('sessionEventDate', DateType::class, array(
                    'widget' => 'single_text',
                    'attr' => array( 'class' => 'datetimepicker'),
                    'format' => 'yyyy-MM-dd HH:mm',
                    'html5' => false,
                ))
                ->add('scheduleItem', ChoiceType::class, array(
                    'label' => 'Órarendi Elem',
                    'mapped' => false,
                    'choices' => $this->scheduleItemCollection,
                    'data' => null,
                    'placeholder' => 'Válasz egy órát',
                    'required' => true,
                ))
                ->add('announcedSession', ChoiceType::class, array(
                    'label' => 'Bejelentkezéses óra',
                    'mapped' => false,
                    'choices' => $this->announcedSessionCollection,
                    'data' => null,
                    'placeholder' => 'Nem bejelentkezéses óra',
                    'required' => false,
                ))
                ->add(
                    'attendees', 'collection', array(
                        'entry_type' => AttendanceHistoryType::class,
                        'label' => 'Bérletek',
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
                ->add('sessionFeeRevenueSold', 'money', array(
                    'currency' => 'HUF',
                    'scale' => 0
                ))
                ->add('save', 'submit', array(
                    'label' => 'Mentés'
                ))
                ->add('saveAndContinue', 'submit', array(
                    'label' => 'Mentés és Folytatás',
                    'attr' => array('class' => 'btn-success')
                ))
                ->add('delete','submit', array(
                    'attr'      => array('class' => 'button-link delete'),
                    'label'     => 'Űrlap Törlése'
                ));
            ;
        } else {
            $builder
                ->add('sessionEventDate', DateType::class, array(
                    'widget' => 'single_text',
                    'attr' => array( 'class' => 'datetimepicker'),
                    'format' => 'yyyy-MM-dd HH:mm',
                    'html5' => false,
                ))
                ->add('scheduleItem')
                ->add('announcedSession')
                ->add(
                    'attendees', 'collection', array(
                        'entry_type' => AttendanceHistoryType::class,
                        'label' => 'Bérletek',
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
                ->add('sessionFeeRevenueSold', 'money', array(
                    'currency' => 'HUF',
                    'scale' => 0
                ))
                ->add('save', 'submit', array(
                    'label' => 'Mentés'
                ))
                ->add('saveAndContinue', 'submit', array(
                    'label' => 'Mentés és Folytatás',
                    'attr' => array('class' => 'btn-success')
                ))
                ->add('delete','submit', array(
                    'attr'      => array('class' => 'button-link delete'),
                    'label'     => 'Űrlap Törlése'
                ));
            ;
        }

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
