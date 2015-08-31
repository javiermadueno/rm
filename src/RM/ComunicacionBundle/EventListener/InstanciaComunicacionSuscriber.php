<?php

namespace RM\ComunicacionBundle\EventListener;


use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\ComunicacionBundle\DependencyInjection\InstanciaComunicacionServicio;
use RM\ComunicacionBundle\Entity\Fases;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\ComunicacionBundle\Event\InstanciaComunicacionEvent;
use RM\ComunicacionBundle\Event\InstanciaComunicacionEvents;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InstanciaComunicacionSubscriber implements EventSubscriberInterface
{

    public function __construct(InstanciaComunicacionServicio $servicio, DoctrineManager $doctrine)
    {
        $this->instanciaService = $servicio;
        $this->em               = $doctrine->getManager();
    }

    public static function getSubscribedEvents()
    {
        return  [
            InstanciaComunicacionEvents::CAMBIO_FASE => ['onCambioFase']
        ];
    }

    public function onCambioFase(InstanciaComunicacionEvent $event)
    {
        $instancia  = $event->getInstancia();
        $codigoFase = $instancia->getFase()->getCodigo();

        switch($codigoFase){
            case InstanciaComunicacion::FASE_CONFIGURACION:
                $this->tramitarACampanya($instancia);
                break;
            default:
                throw new Exception(sprintf("Fase no validad para la instancia de comunicacion %s", $instancia->getIdInstancia()));
        }
    }

    private function tramitarACampanya(InstanciaComunicacion $instancia)
    {
        if(!$this->compruebaFaseConfiguracion($instancia)) {
            return false;
        }

        $faseCampanya = $this->em->getRepository('RMComunicacionBundle:Fases')->findOneBy([
                    'codigo' => InstanciaComunicacion::FASE_NEGOCIACION
        ]);

        if(! $faseCampanya instanceof Fases){
            return false;
        }

        $instancia->setFase($faseCampanya);

        $this->em->persist($instancia);
        $this->em->flush();

        return true;

    }

    public function compruebaFaseConfiguracion(InstanciaComunicacion $instancia)
    {
        if($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_CONFIGURACION) {
            return false;
        }

        /**
         * Se comprueba que por cada GrupoSlots de la plantilla haya un registro en num_promociones
         */
        $grupoSlots = $this->instanciaService
            ->findNumRegistrosNumPromocionesPorGrupoSlotsByIdInstancia($instancia->getIdInstancia());

        foreach($grupoSlots as $grupoSlot)
        {
            if(!intval($grupoSlot['numPro'])) {
                return false;
            }
        }

        /**
         * Se comprueba que el número de genéricas es igual o mayor que el número de slots del grupo
         */
        $numPromociones = $instancia->getNumPromociones();

        foreach($numPromociones as $numPromocion)
        {
            $grupoSlot = $numPromocion->getIdGrupo();
            $numSlot   = $grupoSlot->getNumSlots();

            if($numPromocion->getNumGenericas() < $numSlot) {
                return false;
            }
        }

        return true;
    }
} 