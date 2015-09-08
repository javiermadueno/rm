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
            ->add('descripcion', 'text', ['required' => false])
            ->add('impConsumidor', 'number', ['required' => false])
            ->add('impDistribuidor', 'number', ['required' => false])
            ->add('impFijo', 'number', ['required' => false])
            ->add('condiciones', 'text', ['required' => false])
            ->add('fidelizacion', 'text', ['required' => false])
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
