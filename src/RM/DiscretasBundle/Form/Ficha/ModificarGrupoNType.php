<?php

namespace RM\DiscretasBundle\Form\Ficha;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModificarGrupoNType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add
		('referenciaN', 'number', array(
			'label' => 'Tiempo de referencia N',
			'required' => true,
		));
	}

	public function getName()
	{
		return 'ModificarGrupoNType';
	}
}

