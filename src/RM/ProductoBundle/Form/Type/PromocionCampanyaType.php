<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 14/09/2015
 * Time: 13:23
 */

namespace RM\ProductoBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromocionCampanyaType extends AbstractType
{
    public function getName()
    {
        return 'promocion_campanya';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_type' => 'RM\ProductoBundle\Entity\Promocion'
            ])
            ->setRequired(['em']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

} 