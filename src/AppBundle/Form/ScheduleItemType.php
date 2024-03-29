<?php

namespace AppBundle\Form;

use AppBundle\Entity\ScheduleItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleItemType extends AbstractType
{
	/**
	 * @var ScheduleItem
	 */
	protected $scheduleItem;

	/**
	 * @param ScheduleItem $scheduleItem
	 */
	public function __construct(ScheduleItem $scheduleItem)
	{
		$this->scheduleItem = $scheduleItem;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('scheduledItemName');

		if (!$this->scheduleItem->getIsWeeklyOnline()) {
			$builder
				->add('scheduledDay', ChoiceType::class, array(
					'choices' => array(
						'1' => 'Hétfő',
						'2' => 'Kedd',
						'3' => 'Szerda',
						'4' => 'Csütörtök',
						'5' => 'Péntek',
						'6' => 'Szombat',
						'7' => 'Vasárnap'
					),
					'required' => true,
				))
				->add('scheduledStartTime', 'text', array( //id: appbundle_scheduleitem_scheduledStartTime
					'attr' => array('data-format' => 'HH:mm', 'data-template' => 'HH : mm'),
					'required' => true,
				))
				->add('scheduledDueTime', 'text', array(
					'attr' => array('data-format' => 'HH:mm', 'data-template' => 'HH : mm'),
					'required' => true,
				));
		}

		$builder
			->add('location', 'text')
			->add('session_name', 'text', array(
				'required' => false
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
