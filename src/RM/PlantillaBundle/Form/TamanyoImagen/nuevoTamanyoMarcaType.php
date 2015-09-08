<?php

namespace RM\PlantillaBundle\Form\TamanyoImagen;

use RM\PlantillaBundle\Entity\TamanyoImagen;
use Symfony\Component\Form\FormBuilderInterface;

class nuevoTamanyoMarcaType extends nuevoTamanyoProductoType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('codigo','text', ['required'=>true])
				->add('ancho', 'number', ['required' => true])
				->add('alto', 'number', ['required' => true])
				->add('estado', 'hidden', array(
						'data' => '1',
				))
				->add('tipo', 'hidden', array(
						'data' => TamanyoImagen::MARCA,
				))
		;
	}

	public function getName()
	{
		return '';
	}
}

