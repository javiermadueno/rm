<?php

namespace RM\ProductoBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use RM\PlantillaBundle\Form\DataTransformer\GrupoSlotsToNumberTransformer;
use RM\PlantillaBundle\Form\DataTransformer\InstanciaTransformer;
use RM\ProductoBundle\Entity\NumPromociones;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class NumPromocionesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $grupoTransformer     = new GrupoSlotsToNumberTransformer($options['em']);
        $instanciaTransformer = new InstanciaTransformer($options['em']);

        $builder
            ->add('numSegmentadas', 'integer', [
                'constraints' => [
                    new GreaterThan(['value' => 0]),
                    new NotBlank(),
                ]
            ])
            ->add('numGenericas', 'integer', [
                'constraints' => [
                    new GreaterThan(['value' => 0]),
                    new NotBlank(),
                ]
            ])
            ->add('idCategoria', 'entity', [
                'class'         => 'RMCategoriaBundle:Categoria',
                'em'            => $options['em'],
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('categoria')
                              ->join('categoria.idNivelCategoria', 'nivel')
                              ->where('nivel.idNivelCategoria = :nivel')
                              ->andWhere('categoria.asociado = 1')
                              ->orderBy('categoria.nombre')
                              ->setParameter('nivel', $options['nivel_categoria'])
                        ;

                },
                'empty_value'   => 'select.todas',
                'property'      => 'nombre'
            ])
            ->add($builder->create('idGrupo', 'hidden')->addModelTransformer($grupoTransformer))
            ->add($builder->create('idInstancia', 'hidden')->addModelTransformer($instanciaTransformer))
        ;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => 'RM\ProductoBundle\Entity\NumPromociones',
            'nivel_categoria' => 1
        ])
        ;

        $resolver->setRequired([
            'em',
            'nivel_categoria'
        ])
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_num_promocion';
    }
}
