<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 27/02/2015
 * Time: 9:08
 */

namespace RM\PlantillaBundle\Form\TamanyoImagen;


use RM\PlantillaBundle\Entity\TamanyoImagen;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;

class nuevoTamanyoCreatividadType extends nuevoTamanyoProductoType
{
    public function build(FormBuilderInterface $builder, Options $options)
    {
        $builder->add('codigo','text', ['required'=>true])
            ->add('ancho', 'number', ['required' => true])
            ->add('alto', 'number', ['required' => true])
            ->add('estado', 'hidden', [
                    'data' => '1',
                ])
            ->add('tipo', 'choice', [
                    'choices' => [
                        TamanyoImagen::PRODUCTO    => 'tamanyo.producto',
                        TamanyoImagen::MARCA       => 'tamanyo.marca',
                        TamanyoImagen::CREATIVIDAD => 'tamanyo.creatividad'
                    ]
                ])
        ;
    }

    public function getName()
    {
        return '';
    }

} 