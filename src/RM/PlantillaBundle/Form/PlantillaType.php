<?php

namespace RM\PlantillaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlantillaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('nombre', 'text', [
                    'required' => true
                ])
            ->add('submit', 'submit', ['label' => 'Guardar']);
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
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_plantillabundle_plantilla';
    }
}
