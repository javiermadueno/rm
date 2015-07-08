<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 01/07/2015
 * Time: 13:45
 */
namespace RM\ComunicacionBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\SegmentoComunicacion;
use RM\DiscretasBundle\Entity\Tipo;
use RM\SegmentoBundle\Entity\Segmento;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SegmentoComunicacionType extends AbstractType
{

    public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variable_ciclo_vida', 'entity', [
                'class'         => 'RM\TransformadasBundle\Entity\Vt',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('vt')
                        ->join('vt.tipo', 't')
                        ->where('t.codigo = :ciclo_vida')
                        ->setParameter('ciclo_vida', Tipo::CICLO_VIDA);
                },
                'placeholder'   => 'select.seleccione',
                'property'      => 'nombre',
                'em'            => $options['em'],
                'label'         => 'variable.ciclo.vida',
                'mapped'        => false,
                'attr'          => [
                    'class' => 'change-ajax-submit'
                ]
            ])
            ->add('estado', 'choice', [
                'label' => 'estado',
                'required' => true,
                'choice_list' => $this->getEstados(),
            ])
            ->add('tipo', 'choice', [
                'label'       => 'tipo',
                'required'    => true,
                'choice_list' => $this->getTipoFrecuencia(),
                'attr'        => [
                    'class' => 'change-ajax-submit'
                ]
            ])
            ->add('horaProg', 'time', [
                'label'    => 'hora',
                'required' => true,
                'html5'    => true,
                'widget'   => 'single_text',
                'attr'     => [
                    'pattern'     => "([01]?[0-9]|2[0-3]):[0-5][0-9]",
                    'placeholder' => 'HH:mm'
                ]
            ]);


        $modificaMesesDias = function (FormInterface $form, $tipo) {
            if (!$tipo) {
                return;
            }

            switch ($tipo) {
                case SegmentoComunicacion::FREC_DIARIA:
                    break;
                case SegmentoComunicacion::FREC_SEMANAL:
                    $this->addDiasSemana($form);
                    break;
                case SegmentoComunicacion::FREC_QUINCENAL:
                    $this->addDiasMes($form, 15);
                    break;
                case SegmentoComunicacion::FREC_ANUAL:
                    $this->addDiasMes($form, 31);
                    $this->addMes($form);
                    break;
                default:
                    $this->addDiasMes($form, 31);
            }
        };

        $modificaSegmentos = function (FormInterface $form, $variable_ciclo_vida) use ($options) {
            $form->add('idSegmento', 'entity', [
                'required'      => true,
                'em'            => $options['em'],
                'class'         => 'RM\SegmentoBundle\Entity\Segmento',
                'query_builder' => function (EntityRepository $er) use ($variable_ciclo_vida) {
                    return $er->createQueryBuilder('s')
                        ->where('s.estado > -1')
                        ->andWhere('s.idVt = :idvt')
                        ->setParameter('idvt', $variable_ciclo_vida);
                },
                'property'      => 'nombre',
                'label' => 'segmento'
            ]);
        };

        $modificaFechas = function (FormInterface $form, Comunicacion $comunicacion) {

            $date_format  = 'Y-m-d';
            $options_date = [
                'required' => true,
                'html5'    => true,
                'input'    => 'datetime',
                'widget'   => 'single_text',
                'attr'     => [
                    'placeholder' => 'dd/mm/yyyy',
                    'min'         => $comunicacion->getFecInicio()->format($date_format),
                    'max'         => $comunicacion->getFecFin()->format($date_format),
                    'data-min'    => $comunicacion->getFecInicio()->format($date_format),
                    'data-max'    => $comunicacion->getFecFin()->format($date_format),
                    'class'       => 'datepicker'
                ]
            ];

            $form->add('fecInicio', 'date', array_merge($options_date, ['label' => 'fecha.inicial']));
            $form->add('fecFin', 'date',  array_merge($options_date, ['label' => 'fecha.final']));
        };

        $builder ->get('tipo')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use($modificaMesesDias) {
            $form = $event->getForm()->getParent();
            $tipo = $event->getForm()->getData();

            $form->remove('mes');
            $form->remove('dia');

            $modificaMesesDias($form, $tipo);

        });

        $builder->get('variable_ciclo_vida')->addEventListener(FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($modificaSegmentos) {
                $form                = $event->getForm()->getParent();
                $variable_ciclo_vida = $event->getForm()->getData();

                $modificaSegmentos($form, $variable_ciclo_vida);
            });

        $builder->addEventListener(FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($modificaSegmentos, $modificaFechas, $modificaMesesDias) {
                $form                = $event->getForm();
                $variable_ciclo_vida = $form->get('variable_ciclo_vida')->getData();
                $tipo                = $form->get('tipo')->getData();
                $comunicacion        = $event->getData()->getidComunicacion();

                /** @var Segmento $segmento */
                $segmento            = $event->getData()->getIdSegmento();

                if($segmento instanceof Segmento) {
                    $variable_ciclo_vida = $segmento->getIdVt()->getIdVt();
                    $form->get('variable_ciclo_vida')->setData($segmento->getIdVt());
                }

                if ($variable_ciclo_vida) {
                    $modificaSegmentos($form, $variable_ciclo_vida);
                }

                $modificaMesesDias($form, $tipo);
                $modificaFechas($form, $comunicacion);
            }
        );
    }

    private function getTipoFrecuencia()
    {
        return new ChoiceList(
            [
                SegmentoComunicacion::FREC_DIARIA,
                SegmentoComunicacion::FREC_SEMANAL,
                SegmentoComunicacion::FREC_QUINCENAL,
                SegmentoComunicacion::FREC_MENSUAL,
                SegmentoComunicacion::FREC_TRIMESTRAL,
                SegmentoComunicacion::FREC_CUATRIMESTRAL,
                SegmentoComunicacion::FREC_SEMESTRAL,
                SegmentoComunicacion::FREC_ANUAL
            ],
            [
                'frecuencia.diaria',
                'frecuencia.semanal',
                'frecuencia.quincenal',
                'frecuencia.mensual',
                'frecuencia.trimestral',
                'frecuencia.cuatrimestral',
                'frecuencia.semestral',
                'frecuencia.anual'
            ]
        );
    }

    private function getEstados() {
        return new ChoiceList(
            [
            Comunicacion::ESTADO_CONFIGURACION,
            Comunicacion::ESTADO_ACTIVO,
            Comunicacion::ESTADO_PAUSADO,
            Comunicacion::ESTADO_COMPLETADA
        ],
            [
                'comunicacion.estado.configuracion',
                'comunicacion.estado.activo',
                'comunicacion.estado.pausado',
                'comunicacion.estado.completada'
            ]
        );
    }

    private function addDiasSemana(FormInterface $form)
    {
        $form->add('dia', 'choice', [
            'required' => true,
            'choices'  => [
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
                7 => 'Domingo'
            ]
        ]);

        return $form;
    }

    private function addDiasMes(FormInterface $form, $numero_dias)
    {

        $form->add('dia', 'choice', [
            'required' => true,
            'choices'  => $this->diasMes($numero_dias)
        ]);

        return $form;
    }

    private function diasMes($numero_dias)
    {
        $dias = [];
        for ($i = 1; $i < $numero_dias; $i++) {
            $dias[$i] = $i;
        }

        return $dias;
    }

    private function addMes(FormInterface $form)
    {

        $form->add('mes', 'choice', [
            'required' => true,
            'choices'  => cal_info(CAL_GREGORIAN)['months']
        ]);

        return $form;
    }

    public function getName()
    {
        return 'segmento_comunicacion';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'RM\ComunicacionBundle\Entity\SegmentoComunicacion',
                'em'         => null
            ])
            ->addAllowedTypes([
                'em' => 'Doctrine\Common\Persistence\ObjectManager'
            ])
            ->setRequired(['em']);

    }
} 