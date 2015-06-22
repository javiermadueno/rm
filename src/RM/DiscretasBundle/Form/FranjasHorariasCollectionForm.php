<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 15/06/2015
 * Time: 13:09
 */

namespace RM\DiscretasBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FranjasHorariasCollectionForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('franjas', 'collection', [
                'allow_add'    => false,
                'allow_delete' => false,
                'type'         => new FranjaHorariaType(),
            ])
            ->add('submit', 'submit', [
                'label' => 'boton.guardar'
            ]);
    }

    public function getName()
    {
        return 'franjas';
    }
} 