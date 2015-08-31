<?php

namespace RM\CategoriaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NivelCategoriaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','text', [
                    'required' => true,
                ])
            ->add('asociado', 'checkbox', [
                    'required' => false
                ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\CategoriaBundle\Entity\NivelCategoria'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_categoriabundle_nivelcategoria';
    }
}
