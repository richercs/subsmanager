<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ScheduleItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('scheduledItemName')
            ->add('scheduledDay', ChoiceType::class, array(
                'choices' => array(
                    '1' => 'Monday',
                    '2' => 'Tuesday',
                    '3' => 'Wednesday',
                    '4' => 'Thursday',
                    '5' => 'Friday',
                    '6' => 'Saturday',
                    '7' => 'Sunday'
                ),
                'required' => true,
            ))
            ->add('scheduledStartTime', 'text', array( //id: appbundle_scheduleitem_scheduledStartTime
                'attr' => array( 'data-format'=>'HH:mm', 'data-template'=>'HH : mm'),
                'required' => true,
            ))
            ->add('scheduledDueTime', 'text', array(
                'attr' => array( 'data-format'=>'HH:mm', 'data-template'=>'HH : mm'),
                'required' => true,
            ))
            ->add('location', 'text')
            ->add('session_name', 'text', array(
                'required' => false
            ))
            ->add('save', 'submit', array(
                'label' => 'Save Item'
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
            'data_class' => 'AppBundle\Entity\ScheduleItem'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_scheduleitem';
    }


}
