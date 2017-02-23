<?php

namespace EasyAdminTranslationsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use DateTime;

/**
 * Class HiddenEntityRelationType
 * Form component to list and manage translations
 */
class HiddenEntityRelationType extends AbstractType
{
	protected $em;

	/**
	 * HiddenEntityRelationType constructor.
	 * @param $em
	 */
	public function __construct($em)
	{
		$this->em = $em;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		parent::buildForm($builder, $options);
	}

	public function finishView(FormView $view, FormInterface $form, array $options)
	{
		// Get current value
		$entity = $view->vars['value'];

		// Transform entity to ID
		$entity = (is_object($entity) && method_exists($entity, 'getId')) ? $entity->getId() : (string) $entity;

		// Update value in form
		$view->vars['data'] = $view->vars['value'] = $entity;

		// Call parent finish view
		parent::finishView($view, $form, $options);
	}

	/**
	 * Parent Type
	 * @return mixed
	 */
	public function getParent()
	{
		return HiddenType::class;
	}

	/**
	 * Component name
	 * @return string
	 */
	public function getName()
	{
		return 'hidden_entity_relation';
	}
}
