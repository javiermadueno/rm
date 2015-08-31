<?php

namespace RM\DiscretasBundle\Form\Ficha;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModificarGrupoType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('mesesN', 'number', [
			'label'    => 'tiempo.referencia.n',
			'required' => true,
		]);
	}

	public function getName()
	{
		return 'ModificarGrupoType';
	}
}

