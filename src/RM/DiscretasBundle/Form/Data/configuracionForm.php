<?php

namespace RM\DiscretasBundle\Form\Data;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class configuracion extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('num_maximo_dias_novedad')
				->add('valor')
				->add('fecInicio', 'date', array(
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',
					'required'=> false,
					))
				->add('fecFin', 'date', array(
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',
					'required'=> false,
					//'empty_value' => '',
					//'years' => range(date('Y')-100, date('Y')),
					))
				->add('estado', 'choice', array(
					'choices'   => array('1' => 'Activo', '2' => 'En proceso'),
					'empty_value' => 'Seleccione',
					))
				->add('idCanal', 'entity', array(
					'class' => 'RMComunicacionBundle:Canal'
				))
		;
	}

	public function getName()
	{
		return '';
	}
}
