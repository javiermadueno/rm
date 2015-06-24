<?php

namespace RM\PlantillaBundle\Form\TamanyoImagen;

use RM\PlantillaBundle\Entity\TamanyoImagen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class nuevoTamanyoProductoType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $selected = isset($options['selected'])? $options['selected'] : null;

		$builder->add('codigo', 'text', ['required' => true])
				->add('ancho', 'integer', ['required'=>true])
				->add('alto', 'integer', ['required'=>true])
				->add('estado', 'hidden', array(
						'data' => '1',
				))
                ->add('tipo', 'choice', array(
                        'choices' => array(
                            TamanyoImagen::PRODUCTO     => 'tamano.slot.promocion',
                            TamanyoImagen::CREATIVIDAD  => 'tamano.slot.creatividad'
                        ),
                        'empty_value' => 'select.seleccione.tipo',
                        'required' => true,
                        'data' => $selected,

                ))
		;
	}


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $resolver->setRequired(array(
                'selected'
            ));

        $resolver->addAllowedValues(array(
                'selected' => array(
                    TamanyoImagen::PRODUCTO,
                    TamanyoImagen::CREATIVIDAD
                )
            ));
        $resolver->setDefaults(array(
                'selected' => TamanyoImagen::PRODUCTO
            ));


    }


	public function getName()
	{
		return '';
	}
}

