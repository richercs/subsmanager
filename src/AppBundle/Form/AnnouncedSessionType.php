<?php

namespace AppBundle\Form;

use AppBundle\Entity\AnnouncedSession;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnouncedSessionType extends AbstractType
{
	/**
	 * @var AnnouncedSession
	 */
	protected $announcedSession;

	/**
	 * @var array
	 */
	protected $scheduleItemCollection;

	/**
	 * @var boolean
	 */
	protected $onAddPage;

	/**
	 * @param AnnouncedSession $announcedSession
	 * @param array $scheduleItemCollection
	 * @param bool $onAddPage
	 */
	public function __construct(AnnouncedSession $announcedSession, array $scheduleItemCollection = [], $onAddPage = false)
	{
		$this->announcedSession = $announcedSession;
		$this->scheduleItemCollection = $scheduleItemCollection;
		$this->onAddPage = $onAddPage;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		if ($this->onAddPage) {
			$builder
				->add('scheduleItem', ChoiceType::class, array(
					'label' => 'Órarendi Elem',
					'mapped' => false,
					'choices' => $this->scheduleItemCollection,
					'data' => null,
					'placeholder' => 'Válasz egy órát',
					'required' => true,
				));
		} else {
			$builder
				->add('scheduleItem');
		}
		$builder
			->add('timeOfEvent', DateType::class, array(
				'widget' => 'single_text',
				'attr' => array('class' => 'datetimepicker'),
				'format' => 'yyyy-MM-dd HH:mm',
				'html5' => false,
			))
			->add('timeOfSignUpStart', DateType::class, array(
				'widget' => 'single_text',
				'attr' => array('class' => 'datetimepicker'),
				'format' => 'yyyy-MM-dd HH:mm',
				'html5' => false,
			))
			->add('timeFromFinalized', DateType::class, array(
				'widget' => 'single_text',
				'attr' => array('class' => 'datetimepicker'),
				'format' => 'yyyy-MM-dd HH:mm',
				'html5' => false,
			));

		if ($this->announcedSession->getAnnouncedSessionType() === AnnouncedSession::ANNOUNCED_SESSION_TYPE_SINGLE_LIMITED) {
			$builder
				->add('maxNumberOfSignUps', 'integer', [
					'attr' => [
						'min' => 0
					],
					'required' => true
				]);
		}

		$builder
			->add(
				'signees', 'collection', array(
					'entry_type' => SessionSignUpType::class,
					'label' => 'Bejelentkezések',
					'allow_add' => true,
					'allow_delete' => true,
					'prototype' => true,
					'required' => false,
					'attr' => array(
						'class' => 'session-signup-record',
					),
					'by_reference' => false,
				)
			);

		if ($this->onAddPage) {
			$builder
				->add('saveAndContinue', 'submit', array(
					'label' => 'Mentés és Folytatás',
					'attr' => array('class' => 'btn-success')
				));
		}
		$builder
			->add('save', 'submit', array(
				'label' => 'Mentés'
			))
			->add('delete', 'submit', array(
				'attr' => array('class' => 'button-link delete'),
				'label' => 'Űrlap Törlése'
			));
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
