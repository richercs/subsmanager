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
    protected $scheduleItemCollection;

    /**
     * @var boolean
     */
    protected $onAddPage;

    /**
     * @var integer
     */
    private $announcedSessionOwnId;

    /**
     * Constructor.
     *
     ** @param array $scheduleItemCollection
     ** @param boolean $onAddPage
     ** @param integer $announcedSessionOwnId
     */
    public function __construct($scheduleItemCollection = array(), $onAddPage = false, $announcedSessionOwnId = null)
    {
        $this->scheduleItemCollection = $scheduleItemCollection;
        $this->onAddPage = $onAddPage;
        $this->announcedSessionOwnId = $announcedSessionOwnId;
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
                ->add('announcedSession', EntityType::class, array(
                    'class' => 'AppBundle\Entity\AnnouncedSession',
                    'choice_label' => function ($announcedSession) {
                        return $announcedSession->__toString();
                    },
                    'query_builder' => function (AnnouncedSessionRepository $announcedSessionRepository) {
                        return $announcedSessionRepository->createQueryBuilder('announced_session')
                            ->where('announced_session.sessionEvent is null')
                            ->orWhere('announced_session.sessionEvent = :ownId')
                            ->setParameter('ownId', $this->announcedSessionOwnId);
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
