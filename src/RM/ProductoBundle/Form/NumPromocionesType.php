<?php

namespace RM\ProductoBundle\Form;

use RM\PlantillaBundle\Form\DataTransformer\GrupoSlotsToNumberTransformer;
use RM\PlantillaBundle\Form\DataTransformer\InstanciaTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumPromocionesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $categorias = $options['categorias'];
        $grupoTransformer = new GrupoSlotsToNumberTransformer($em);
        $instanciaTransformer = new InstanciaTransformer($em);

        $builder
            ->add('numSegmentadas', 'number', array(
                    'required' => true
                ))
            ->add('numGenericas', 'number', array(
                    'required' => true
                ))
            ->add('estado', 'hidden', array('data' => 1))
            ->add($builder->create('idInstancia', 'hidden')->addModelTransformer($instanciaTransformer))
            ->add($builder->create('idGrupo', 'hidden')->addModelTransformer($grupoTransformer))
            ->add('idCategoria', 'entity', array(
                    'class' => 'RM\CategoriaBundle\Entity\Categoria',
                    'em' => $_SESSION['connection'],
                    'choices' => $categorias,
                    'property' => 'nombre',
                    'empty_value' => '- Categoria -',
                    'empty_data' => null,
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
            'data_class' => 'RM\ProductoBundle\Entity\NumPromociones',
            'categorias' => array()
        ));

        $resolver->setRequired(array(
                'categorias',
                'em'
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_productobundle_numpromociones';
    }
}
