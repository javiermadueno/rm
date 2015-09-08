<?php

namespace RM\DiscretasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VidSegmentoGlobalType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'text', array(
                    'required' => true,
                ))
            ->add('condicion', 'choice', array(
                    'choices' => array(
                        '1' => '<',
                        '2' => '<='
                    ),
                    'required' => true
                ))
            ->add('pivote', 'integer', array(
                    'required' => true
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'RM\DiscretasBundle\Entity\VidSegmentoGlobal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_discretasbundle_vidsegmentoglobal';
    }
}
