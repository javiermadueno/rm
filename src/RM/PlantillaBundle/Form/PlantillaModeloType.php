<?php

namespace RM\PlantillaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlantillaModeloType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('nombre', 'text', [
                'required' => true
            ])
            ->add('descripcion', 'text', [
                    'required' => false
                ])
            ->add('canal', 'entity', [
                    'class' => 'RMComunicacionBundle:Canal',
                    'em' => $options['em'],
                    'required' => true,
                    'placeholder' => '- Seleccione un canal -',
                ])
            ->add('estado', 'hidden', ['data' => 1])
            ->add('esModelo', 'hidden', ['data' => true])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'RM\PlantillaBundle\Entity\Plantilla'
            ])
            ->setRequired(['em'])
            ->setAllowedTypes('em' , 'Doctrine\Common\Persistence\ObjectManager')
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_plantillabundle_plantillamodelo';
    }
}
