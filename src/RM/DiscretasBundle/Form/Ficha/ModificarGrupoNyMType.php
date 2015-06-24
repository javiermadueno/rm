<?php

namespace RM\DiscretasBundle\Form\Ficha;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModificarGrupoNyMType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('mesesN', 'number', array(
			'label' => 'Tiempo de referencia N',
			'required' => true,
		))
		->add('mesesM', 'number', array(
				'label' => 'Tiempo de referencia M',
				'required' => true,
		));
	}

	public function getName()
	{
		return 'ModificarGrupoNyMType';
	}
}

