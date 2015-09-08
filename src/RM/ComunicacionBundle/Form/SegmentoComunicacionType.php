<?php

namespace RM\ComunicacionBundle\Form;

use Doctrine\ORM\EntityRepository;
use RM\ComunicacionBundle\Entity\SegmentoComunicacion;
use RM\ComunicacionBundle\Form\DataTransformer\DatetimeToStringTransformer;
use RM\PlantillaBundle\Form\DataTransformer\ComunicacionToNumberTransformer;
use RM\TransformadasBundle\Entity\Vt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SegmentoComunicacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $comunicacionTransformer = new ComunicacionToNumberTransformer($options['em']);
        $datetimeTransformer = new DatetimeToStringTransformer('d/m/Y');


        $builder
            ->add(
                $builder->create(
                    'fecInicio',
                    'text',
                    [
                        'required' => true,
                        'attr'     => [
                            'class' => 'datepicker'
                        ]
                    ]
                )->addViewTransformer($datetimeTransformer)
            )
            ->add(
                $builder->create(
                    'fecFin',
                    'text',
                    [
                        'required' => true,
                        'attr'     => [
                            'class' => 'datepicker'
                        ]
                    ]
                )->addViewTransformer($datetimeTransformer)
            )
            ->add(
                'horaProg',
                'time',
                [
                    'required' => true
                ]
            )
            ->add(
                'tipo',
                'choice',
                [
                    'required'   => true,
                    'choices'    => $this->getTipos(),
                    'empty_data' => 'Seleccione una frecuencia',
                ]
            )
            ->add('dia')
            ->add('mes')
            ->add(
                'estado',
                'hidden',
                [
                    'data' => 1
                ]
            )
            ->add($builder->create('idComunicacion', 'hidden')->addModelTransformer($comunicacionTransformer))
            ->add(
                'idCicloVida',
                'entity',
                [
                    'mapped'        => false,
                    'em'            => $_SESSION['connection'],
                    'class'         => 'RM\TransformadasBundle\Entity\Vt',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('v')
                            ->where('v.tipo = :tipo')
                            ->andWhere('v.estado > -1')
                            ->setParameter('tipo', Vt::TIPO_CICLO_VIDA);
                    },
                    'property'      => 'nombre',
                    'empty_value'   => 'Seleccione variable',
                    'empty_data'    => null,
                    'label'         => 'Ciclo de vida'
                ]
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'data_class' => 'RM\ComunicacionBundle\Entity\SegmentoComunicacion'
                ]
            )
            ->setRequired(['em'])
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rm_comunicacionbundle_segmentocomunicacion';
    }

    private function getTipos()
    {
        return [
            SegmentoComunicacion::FREC_DIARIA        => 'frecuencia.diaria',
            SegmentoComunicacion::FREC_SEMANAL       => 'frecuencia.semanal',
            SegmentoComunicacion::FREC_QUINCENAL     => 'frecuencia.quincenal',
            SegmentoComunicacion::FREC_MENSUAL       => 'frecuencia.mensual',
            SegmentoComunicacion::FREC_TRIMESTRAL    => 'frecuencia.trimestral',
            SegmentoComunicacion::FREC_CUATRIMESTRAL => 'frecuencia.cuatrimestral',
            SegmentoComunicacion::FREC_SEMESTRAL     => 'frecuencia.semestral',
            SegmentoComunicacion::FREC_ANUAL         => 'frecuencia.anual'
        ];
    }
}
