<?php

namespace RM\TransformadasBundle\Form;

use RM\TransformadasBundle\Entity\VtSegmento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VtType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('segmentos', 'collection', [
                'type' => new VtSegmentoType(),
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\TransformadasBundle\Entity\Vt'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_transformadasbundle_vt';
    }
}
