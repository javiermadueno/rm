<?php

namespace RM\InsightBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BuscadorCampanyasType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var EntityManager $em */
        $em = $options['em'];
        $canal  = isset($options['canal']) ? $options['canal'] : null;

        $builder
            ->add('comunicacion', 'entity', [
                'class' => 'RMComunicacionBundle:Comunicacion',
                'query_builder' => function (EntityRepository $er) use ($em, $canal) {
                    $qb =  $er->createQueryBuilder('comunicacion')
                        ->distinct(true)
                        ->join('RMComunicacionBundle:SegmentoComunicacion', 'seg', 'WITH', 'seg.idComunicacion = comunicacion.idComunicacion')
                        ->join('RMComunicacionBundle:InstanciaComunicacion', 'i', 'WITH', 'i.idSegmentoComunicacion = seg.idSegmentoComunicacion')
                        ->join('i.fase', 'fase')
                        ->where('fase.codigo = :fase_finalizada')
                        ->andWhere('comunicacion.estado > -1')
                        ->setParameter('fase_finalizada', InstanciaComunicacion::FASE_FINALIZADA);

                    if(!is_null($canal)) {
                        $qb->andWhere('comunicacion.idCanal = id_canal')
                            ->setParameter('id_canal', $canal);
                    }

                    return $qb;
                },
                'choice_label' => 'nombre',
                'em' => $em,
                'placeholder' => 'select.seleccione',
                'required' => $options['comunicacion_requerida']
            ])
            ->add('fechaInicio', 'date', [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'datepicker'
                ],
                'required' => false
            ])
            ->add('fechaFin', 'date', [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'datepicker'
                ],
                'required' => false
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setRequired(['em']);
        $resolver->setDefaults([
            'canal' =>null,
            'data_class' => 'RM\InsightBundle\Filter\InstanciaComunicacionFilter',
            'comunicacion_requerida' => false
        ]);
    }

    public function getName()
    {
        return 'buscador_campanya';
    }

} 