<?php

namespace RM\ComunicacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreatividadType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text')
            ->add('descripcion')
            ->add('tamanyoImagen', 'tamanyoImagen')
            ->add('estado', 'hidden', [
                'data' => 1
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ComunicacionBundle\Entity\Creatividad',
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_comunicacionbundle_creatividad';
    }
}
