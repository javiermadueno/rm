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
            ->add('nombre', 'text', [
                    'required' => true,
                ])
            ->add('condicion', 'choice', [
                    'choices' => [
                        '1' => '<',
                        '2' => '<='
                    ],
                    'required' => true
                ])
            ->add('pivote', 'integer', [
                    'required' => true
                ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\DiscretasBundle\Entity\VidSegmentoGlobal'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_discretasbundle_vidsegmentoglobal';
    }
}
