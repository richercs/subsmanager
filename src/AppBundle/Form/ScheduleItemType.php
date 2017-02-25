<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
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
            ->add('scheduledDate', DateType::class, array(
                'widget' => 'single_text',
                'attr' => array( 'class' => 'datetimepicker'),
                'format' => 'yyyy-MM-dd HH:mm',
                'html5' => false,
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
