<?php

namespace RM\TransformadasBundle\Form;

use RM\TransformadasBundle\Entity\VtGrupo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VtSegmentoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', ['required' => true])
            ->add('grupos', 'collection', [
                'type' => new VtGrupoType(),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => true,
                'prototype' => new VtGrupo(),
                'prototype_name' => '__grupo__'
            ])

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\TransformadasBundle\Entity\VtSegmento'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_transformadasbundle_vtsegmento';
    }
}
