<?php

namespace RM\ComunicacionBundle\Form\Gestion;

use RM\ComunicacionBundle\Entity\Comunicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class nuevaComunicacionType extends AbstractType
{
    private $comunicacion;


    private $estados = [
        Comunicacion::ESTADO_CONFIGURACION => 'En configuración',
        Comunicacion::ESTADO_ACTIVO        => 'Activa',
        Comunicacion::ESTADO_PAUSADO       => 'Pausada',
        Comunicacion::ESTADO_COMPLETADA    => 'Completada'
    ];

    public function __construct(Comunicacion $comunicacion = null)
    {
        $this->comunicacion = $comunicacion;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre', 'text', [
            'required' => true
        ])
            ->add('fecInicio', 'date', [
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'required' => true,
            ])
            ->add('fecFin', 'date', [
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy',
                'required' => true,
            ])
            ->add('estado', 'choice', [
                'choices'     => $this->getEstadosPosibles(),
            ])
            ->add('idCanal', 'entity', [
                'class'       => 'RMComunicacionBundle:Canal',
                'empty_value' => 'Seleccione un canal',
                'em'          => $options['em'],
                'required'    => true
            ]);
    }

    /**
     * Devuelve un array con los estados a los que puede pasar una comunicación
     *
     * @return array
     */
    private function getEstadosPosibles()
    {
        if (!$this->comunicacion instanceof Comunicacion) {
            return $this->estados;
        }

        if ($this->comunicacion->getGenerada() || in_array($this->comunicacion->getEstado(), [
                Comunicacion::ESTADO_ACTIVO,
                Comunicacion::ESTADO_PAUSADO,
                Comunicacion::ESTADO_COMPLETADA
            ])
        ) {
            return [
                Comunicacion::ESTADO_ACTIVO     => 'Activa',
                Comunicacion::ESTADO_PAUSADO    => 'Pausada',
                Comunicacion::ESTADO_COMPLETADA => 'Completada'
            ];
        }

        return $this->estados;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'RM\ComunicacionBundle\Entity\Comunicacion'
            ])
            ->setRequired(['em'])
            ->setAllowedTypes('em','Doctrine\Common\Persistence\ObjectManager');
    }

    public function getName()
    {
        return 'nueva_comunicacion';
    }
}

