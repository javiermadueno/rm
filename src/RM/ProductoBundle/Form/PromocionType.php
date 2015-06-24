<?php

namespace RM\ProductoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PromocionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('descripcion', 'text')
            ->add('impConsumidor', 'number')
            ->add('impDistribuidor', 'number')
            ->add('impFijo', 'number')
            ->add('condiciones', 'text')
            ->add('fidelizacion', 'text')
            ->add('codigo', 'text')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RM\ProductoBundle\Entity\Promocion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_productobundle_promocion';
    }
}
