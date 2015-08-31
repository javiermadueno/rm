<?php

namespace RM\LinealesBundle\Form\Data;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LinealBuscadorType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('nombre')
		->add('page', 'hidden', [
				'data'   => '1',
				'mapped' => false,
		]);
	}

	public function getName()
	{
		return '';
	}
}

