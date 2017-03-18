<?php

namespace AppBundle\Form;

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
            ->add('owner') // TODO: How to not show a default value? / This should be a text field where you start typing and it filters the possible names
            ->add('buyer')
            ->add('isMonthlyTicket')
            ->add('start_date', DateType::class, array(
                'widget' => 'single_text',
                'attr' => array( 'class' => 'datetimepicker'),
                'format' => 'yyyy-MM-dd HH:mm',
                'html5' => false,
            ))
            ->add('price')
            ->add('save', 'submit', array(
                'label' => 'Save Subscription'
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
            'data_class' => 'AppBundle\Entity\Subscription'
        ));
    }
}
