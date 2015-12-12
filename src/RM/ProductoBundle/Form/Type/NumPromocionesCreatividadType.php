<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 21/08/2015
 * Time: 12:06
 */

namespace RM\ProductoBundle\Form\Type;


use Doctrine\Tests\ORM\Tools\ResolveTargetEntityListenerTest;
use RM\PlantillaBundle\Form\DataTransformer\GrupoSlotsToNumberTransformer;
use RM\PlantillaBundle\Form\DataTransformer\InstanciaTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NumPromocionesCreatividadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];

        $instanciaTransformer = new InstanciaTransformer($em);
        $grupoTransformer = new GrupoSlotsToNumberTransformer($em);

        $builder
            ->add('numSegmentadas', 'integer', [])
            ->add('numGenericas', 'integer', [])
            ->add($builder->create('idInstancia', 'hidden')->addModelTransformer($instanciaTransformer))
            ->add($builder->create('idGrupo', 'hidden')->addModelTransformer($grupoTransformer))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RM\ProductoBundle\Entity\NumPromociones'
        ]);

        $resolver->setRequired(['em']);
    }

    public function getName()
    {
        return 'num_promocion_creatividad';
    }
} 