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
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $categorias = $options['categorias'];
        $grupoTransformer = new GrupoSlotsToNumberTransformer($em);
        $instanciaTransformer = new InstanciaTransformer($em);

        $builder
            ->add('numSegmentadas', 'number', [
                'required' => true
            ])
            ->add('numGenericas', 'number', [
                'required' => true
            ])
            ->add('estado', 'hidden', ['data' => 1])
            ->add($builder->create('idInstancia', 'hidden')->addModelTransformer($instanciaTransformer))
            ->add($builder->create('idGrupo', 'hidden')->addModelTransformer($grupoTransformer))
            ->add('idCategoria', 'entity', [
                'class'       => 'RM\CategoriaBundle\Entity\Categoria',
                'em'          => $_SESSION['connection'],
                'choices'     => $categorias,
                'property'    => 'nombre',
                'empty_value' => '- Categoria -',
                'empty_data'  => null,
                'required'    => true
            ]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ProductoBundle\Entity\NumPromociones',
            'categorias' => []
        ]);

        $resolver->setRequired([
            'categorias',
            'em'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_productobundle_numpromociones';
    }
}
