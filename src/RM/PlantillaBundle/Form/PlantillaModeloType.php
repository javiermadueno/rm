<?php

namespace RM\PlantillaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlantillaModeloType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = isset($options['em'])? $options['em'] : $_SESSION['connection'];

        $builder->add('nombre', 'text', array(
                'required' => true
            ))
            ->add('descripcion', 'text', array(
                    'required' => false
                ))
            ->add('canal', 'entity', array(
                    'class' => 'RMComunicacionBundle:Canal',
                    'em' => $em,
                    'required' => true,
                    'empty_value' => '- Seleccione un canal -',
                ))
            ->add('estado', 'hidden', array('data' => 1))
            ->add('esModelo', 'hidden', array('data' => true))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RM\PlantillaBundle\Entity\Plantilla'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_plantillabundle_plantillamodelo';
    }
}
