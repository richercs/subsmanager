<?php


namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnouncedSessionType extends AbstractType
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
     * Constructor.
     *
     */
    public function __construct(array $scheduleItemCollection = array(), $onAddPage = false)
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
                ->add('scheduleItem', ChoiceType::class, array(
                    'label' => 'Órarendi Elem',
                    'mapped' => false,
                    'choices' => $this->scheduleItemCollection,
                    'data' => null,
                    'placeholder' => 'Válasz egy órát',
                    'required' => true,
                ))
                ->add('timeOfEvent', DateType::class, array(
                    'widget' => 'single_text',
                    'attr' => array( 'class' => 'datetimepicker'),
                    'format' => 'yyyy-MM-dd HH:mm',
                    'html5' => false,
                ))
                ->add('timeFromFinalized', DateType::class, array(
                    'widget' => 'single_text',
                    'attr' => array( 'class' => 'datetimepicker'),
                    'format' => 'yyyy-MM-dd HH:mm',
                    'html5' => false,
                ))
                ->add('maxNumberOfSignUps')
                ->add(
                    'signups', 'collection', array(
                        'entry_type' => SessionSignUpsType::class,
                        'label' => 'Bejelentkezések',
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'required'     => false,
                        'attr' => array(
                            'class' => 'session-signup-record',
                        ),
                        'by_reference' => false,
                    )
                )
                ->add(
                    'signupsOnWaitList', 'collection', array(
                        'entry_type' => SessionSignUpsType::class,
                        'label' => 'Várólistás bejelentkezések',
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'required'     => false,
                        'attr' => array(
                            'class' => 'session-signup-record',
                        ),
                        'by_reference' => false,
                    )
                )
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
                ->add('scheduleItem')
                ->add('timeOfEvent', DateType::class, array(
                    'widget' => 'single_text',
                    'attr' => array( 'class' => 'datetimepicker'),
                    'format' => 'yyyy-MM-dd HH:mm',
                    'html5' => false,
                ))
                ->add('timeFromFinalized', DateType::class, array(
                    'widget' => 'single_text',
                    'attr' => array( 'class' => 'datetimepicker'),
                    'format' => 'yyyy-MM-dd HH:mm',
                    'html5' => false,
                ))
                ->add('maxNumberOfSignUps')
                ->add(
                    'signups', 'collection', array(
                        'entry_type' => SessionSignUpsType::class,
                        'label' => 'Bejelentkezések',
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'required'     => false,
                        'attr' => array(
                            'class' => 'session-signup-record',
                        ),
                        'by_reference' => false,
                    )
                )
                ->add(
                    'signupsOnWaitList', 'collection', array(
                        'entry_type' => SessionSignUpsType::class,
                        'label' => 'Várólistás bejelentkezések',
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'required'     => false,
                        'attr' => array(
                            'class' => 'session-signup-record',
                        ),
                        'by_reference' => false,
                    )
                )
                ->add('save', 'submit', array(
                    'label' => 'Mentés'
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
            'data_class' => 'AppBundle\Entity\AnnouncedSession'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_announced_session';
    }

}