<?php

namespace RM\DiscretasBundle\Form\Data;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DiscretaBuscadorType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('nombre');
	}

	public function getName()
	{
		return '';
	}
}

