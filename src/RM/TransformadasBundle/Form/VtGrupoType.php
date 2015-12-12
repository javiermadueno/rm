<?php

namespace RM\TransformadasBundle\Form;

use RM\TransformadasBundle\Entity\VtIntervalo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VtGrupoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intervalos', 'collection', [
                'type' => new VtIntervaloType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__intervalo__'
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\TransformadasBundle\Entity\VtGrupo'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_transformadasbundle_vtgrupo';
    }
}
