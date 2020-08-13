<?php

namespace AppBundle\Form;

use AppBundle\Repository\AnnouncedSessionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
     ** @param array $scheduleItemCollection
     ** @param boolean $onAddPage
     */
    public function __construct($scheduleItemCollection = array(), $onAddPage = false)
    {
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
                ->add('announcedSession', EntityType::class, array(
                    'class' => 'AppBundle\Entity\AnnouncedSession',
                    'choice_label' => function ($announcedSession) {
                        return $announcedSession->__toString();
                    },
                    'query_builder' => function (AnnouncedSessionRepository $announcedSessionRepository) {
                        return $announcedSessionRepository->createQueryBuilder('announced_session')
                            ->where('announced_session.sessionEvent is null');
                    },
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
