<?php

namespace RM\PlantillaBundle\Form\TamanyoImagen;

use RM\PlantillaBundle\Entity\TamanyoImagen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class nuevoTamanyoProductoType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $selected = isset($options['selected'])? $options['selected'] : null;

		$builder->add('codigo', 'text', ['required' => true])
				->add('ancho', 'integer', ['required'=>true])
				->add('alto', 'integer', ['required'=>true])
				->add('estado', 'hidden', [
						'data' => '1',
				])
                ->add('tipo', 'choice', [
                        'choices' => [
                            TamanyoImagen::PRODUCTO     => 'tamano.slot.promocion',
                            TamanyoImagen::CREATIVIDAD  => 'tamano.slot.creatividad'
                        ],
                        'empty_value' => 'select.seleccione.tipo',
                        'required' => true,
                        'data' => $selected,

                ])
		;
	}


    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setRequired([
                'selected'
            ]);

        $resolver->addAllowedValues(
                'selected',[
                    TamanyoImagen::PRODUCTO,
                    TamanyoImagen::CREATIVIDAD
                ]
            );
        $resolver->setDefaults([
                'selected' => TamanyoImagen::PRODUCTO
            ]);


    }


	public function getName()
	{
		return 'tamanyo_imagen';
	}
}

