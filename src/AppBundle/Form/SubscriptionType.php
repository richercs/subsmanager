<?php

namespace AppBundle\Form;

use AppBundle\Entity\Subscription;
use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
	/**
	 * @var Subscription
	 */
	protected $subscription;

	/**
	 * @param Subscription $subscription
	 */
	public function __construct(Subscription $subscription)
	{
		$this->subscription = $subscription;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('owner', AutocompleteType::class, array(
				'class' => 'AppBundle:UserAccount'
			));

		if ($this->subscription->getSubscriptionType() === Subscription::SUBSCRIPTION_TYPE_ATTENDANCE) {
			$builder
				->add('attendanceCount');
		} else {
			$builder
				->add('credit');
		}

		$builder
			->add('startDate', DateType::class, array(
				'widget' => 'single_text',
				'attr' => array('class' => 'datetimepicker'),
				'format' => 'yyyy-MM-dd HH:mm',
				'html5' => false,
			))
			->add('dueDate', DateType::class, array(
				'widget' => 'single_text',
				'attr' => array('class' => 'datetimepicker'),
				'format' => 'yyyy-MM-dd HH:mm',
				'html5' => false,
			))
			->add('numberOfExtensions', 'integer', array(
				'disabled' => true,
				'label' => 'Hosszabítások Száma'
			))
			->add('price', 'money', array(
				'currency' => 'HUF',
				'scale' => 0
			))
			->add('save', 'submit', array(
				'label' => 'Mentés'
			))
			->add('delete', 'submit', array(
				'attr' => array('class' => 'button-link delete'),
				'label' => 'Törlés'
			));
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

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'app_subscription';
	}
}
