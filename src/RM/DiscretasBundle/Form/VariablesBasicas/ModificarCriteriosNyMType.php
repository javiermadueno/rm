<?php

namespace RM\DiscretasBundle\Form\VariablesBasicas;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModificarCriteriosNyMType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('referenciaN', 'integer', array(
				'label' => 'Tiempo Referencia N',
				'required' => true,
		))
		->add('mesesN', 'integer', array(
			'label' => 'N',
			'required' => true,
		))
		->add('mesesM', 'integer', array(
				'label' => 'M',
				'required' => true,
		));
	}

	public function getName()
	{
		return '';
	}
}

