<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 06/11/2015
 * Time: 10:26
 */

namespace RM\ComunicacionBundle\Form\Type;


use RM\ComunicacionBundle\Entity\Canal;
use RM\ComunicacionBundle\Entity\Comunicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComunicacionType extends AbstractType
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
        $builder
            ->add('nombre', 'text', [
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
                'choices' => $this->getEstadosPosibles(),
            ])
            ->add('idCanal', 'entity', [
                'class'       => 'RMComunicacionBundle:Canal',
                'placeholder' => 'Seleccione un canal',
                'em'          => $options['em'],
                'required'    => true
            ])
        ;

        $addAsunto = function(FormInterface $form, Canal $canal){

            if($canal->getNombre() === 'Email') {
                $form->add('asunto', 'text', ['required' =>true,]);
            } else {
                $form->remove('asunto');
            }
        };

        $builder->get('idCanal')->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($addAsunto) {
            $form = $event->getForm()->getParent();
            $canal = $event->getData();

            if(!$canal instanceof Canal) {
                return;
            }

            $addAsunto($form, $canal);
        } );

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($addAsunto){
            $form = $event->getForm();
            $comunicacion = $event->getData();

            if(!$comunicacion instanceof Comunicacion) {
                return;
            }

            $canal = $comunicacion->getIdCanal();
            if(!$canal instanceof Canal) {
                return;
            }

            $addAsunto($form, $canal);

        });


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
            ->setAllowedTypes('em', 'Doctrine\Common\Persistence\ObjectManager');
    }




    public function getName()
    {
        return 'comunicacion';
    }

} 