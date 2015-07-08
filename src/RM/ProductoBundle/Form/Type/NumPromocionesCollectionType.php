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


class NumPromocionesCollectionType extends AbstractType
{

    public function build(FormBuilderInterface $builder, array $options)
    {
        $builder->add('num_promociones', 'collection', [
            'allow_delete' => true,
            'allow_add' => true,
            'type' => new NumPromocionesType()
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'num_promociones';
    }
}