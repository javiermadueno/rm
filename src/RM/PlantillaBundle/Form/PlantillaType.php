<?php

namespace RM\PlantillaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('nombre', 'text', array(
                    'required' => true
                ))
            ->add('submit', 'submit', array('label' => 'Guardar'));
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'RM\PlantillaBundle\Entity\Plantilla'
                ))
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
