<?php

namespace RM\PlantillaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlantillaModeloType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = isset($options['em']) ? $options['em'] : $_SESSION['connection'];

        $builder->add('nombre', 'text', [
            'required' => true
        ])
            ->add('descripcion', 'text', [
                'required' => false
            ])
            ->add('canal', 'entity', [
                'class'       => 'RMComunicacionBundle:Canal',
                'em'          => $em,
                'required'    => true,
                'empty_value' => '- Seleccione un canal -',
            ])
            ->add('estado', 'hidden', ['data' => 1])
            ->add('esModelo', 'hidden', ['data' => true]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\PlantillaBundle\Entity\Plantilla'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_plantillabundle_plantillamodelo';
    }
}
