<?php
/**
 * Created by PhpStorm.
 * User: Javi
 * Date: 17/5/15
 * Time: 16:43
 */

namespace RM\CategoriaBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NivelCategoriaCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('niveles', 'collection', [
                'required'  => true,
                'allow_add' => true,
                'type'      => new NivelCategoriaType(),
            ])
            ->add('submit', 'submit', [
                'label' => 'boton.guardar'
            ]);
    }

    public function getName()
    {
        return 'rm_categoriabundle_niveles_categoria';
    }
} 