<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 29/06/2015
 * Time: 16:21
 */

namespace RM\ProductoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Valid;


class NumPromocionesCollectionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('num_promocion', 'collection', [
            'type' => new NumPromocionesType(),
            'allow_add' => true,
            'allow_delete' => true,
            'options' => [
                'em' => $options['em'],
                'nivel_categoria' => $options['nivel_categoria']
            ],
            'label' => false,
            'error_bubbling' => false,
            'constraints' => [
                new Valid()
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'nivel_categoria' => 1
        ]);

        $resolver->setRequired([
            'em',
            'nivel_categoria'
        ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'rm_num_promociones_collection';
    }
}