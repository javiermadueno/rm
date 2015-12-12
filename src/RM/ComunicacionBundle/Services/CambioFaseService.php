<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 02/09/2015
 * Time: 12:37
 */

namespace RM\ComunicacionBundle\Services;


use Doctrine\ORM\EntityManager;
use RM\ComunicacionBundle\Entity\Fases;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\ProcesosBundle\Entity\TipoProceso;
use RM\ProcesosBundle\Factory\ProcesoFactory;
use RM\ProductoBundle\Entity\NumPromociones;
use RM\ProductoBundle\Entity\Promocion;


class CambioFaseService
{
    private $em;

    private $repository;

    private $procesosFactory;

    public function __construct(EntityManager $em, ProcesoFactory $procesoFactory)
    {
        $this->em              = $em;
        $this->repository      = $em->getRepository('RMComunicacionBundle:InstanciaComunicacion');
        $this->procesosFactory = $procesoFactory;
    }

    /**
     * @param $instancia
     *
     * @return bool
     */
    public function cambioFase($instancia)
    {
        if (!$instancia instanceof InstanciaComunicacion) {
            $instancia = $this->repository->find($instancia);
        }

        $fase = $instancia->getFase()->getCodigo();

        switch ($fase) {
            case InstanciaComunicacion::FASE_CONFIGURACION:
                return $this->tramitarACampanya($instancia);
                break;
            case InstanciaComunicacion::FASE_NEGOCIACION:
                return $this->tramitarASimulacion($instancia);
                break;
            case InstanciaComunicacion::FASE_CIERRE:
                return $this->tramitarAGeneracion($instancia);
                break;
            case InstanciaComunicacion::FASE_CONFIRMACION:
                return $this->tramitarAFinalizada($instancia);
                break;
            default:
                return false;
        }
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function tramitarACampanya(InstanciaComunicacion $instancia)
    {
        if (!$this->compruebaFaseConfiguracion($instancia)) {
            return false;
        }

        $faseCampanya = $this->em
            ->getRepository('RMComunicacionBundle:Fases')
            ->findOneBy(['codigo' => InstanciaComunicacion::FASE_NEGOCIACION]);

        return $this->cambiaFase($instancia, $faseCampanya);
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function compruebaFaseConfiguracion(InstanciaComunicacion $instancia)
    {
        if ($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_CONFIGURACION) {
            return false;
        }

        if (!$instancia->isTodosGruposRellenos()) {
            return false;
        }

        if (!$instancia->isTodasGenericasDefinidas()) {
            return false;
        }

        return true;

    }

    /**
     * @param InstanciaComunicacion $instancia
     * @param Fases                 $fase
     *
     * @return bool
     */
    private function cambiaFase(InstanciaComunicacion $instancia, Fases $fase)
    {
        try {
            $instancia
                ->setFase($fase)
                ->setEstado(1);

            $this->em->persist($instancia);
            $this->em->flush();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function tramitarASimulacion(InstanciaComunicacion $instancia)
    {
        if (!$this->compruebaFaseCampanya($instancia)) {
            return false;
        }

        $faseSimulacion = $this->em
            ->getRepository('RMComunicacionBundle:Fases')
            ->findOneBy(['codigo' => InstanciaComunicacion::FASE_SIMULACION]);

        $this->procesosFactory
            ->createProcesoTipo(TipoProceso::P03);

        return $this->cambiaFase($instancia, $faseSimulacion);

    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function compruebaFaseCampanya(InstanciaComunicacion $instancia)
    {
        if ($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_NEGOCIACION) {
            return false;
        }

        if (!$instancia->isTotalGenericasMayorQueNumeroSlot()) {
            return false;
        }

        return true;
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function tramitarAGeneracion(InstanciaComunicacion $instancia)
    {
        if (!$this->compruebaFaseCierre($instancia)) {
            return false;
        }

        $faseGeneracion = $this->em
            ->getRepository('RMComunicacionBundle:Fases')
            ->findOneBy(['codigo' => InstanciaComunicacion::FASE_GENERACION]);

        $this->procesosFactory->createProcesoTipo(TipoProceso::P04);

        if ($instancia->getTotalPromocionesRechazadas() > 0) {
            $instancia->setPaso(InstanciaComunicacion::PASO_1);

            return $this->cambiaFase($instancia, $faseGeneracion);
        }

        $instancia->setPaso(InstanciaComunicacion::PASO_2);

        return $this->cambiaFase($instancia, $faseGeneracion);
    }

    /**
     * @param InstanciaComunicacion $instancia
     *
     * @return bool
     */
    public function compruebaFaseCierre(InstanciaComunicacion $instancia)
    {
        if ($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_CIERRE) {
            return false;
        }

        if (!$instancia->isNingunaPromocionPendiente()) {
            return false;
        }

        return true;
    }

    public function tramitarAFinalizada(InstanciaComunicacion $instancia)
    {
        $faseFinalizada = $this->em
            ->getRepository('RMComunicacionBundle:Fases')
            ->findOneBy(['codigo' => InstanciaComunicacion::FASE_FINALIZADA]);

        if ($instancia->isEnvioInmediato()) {
            $this
                ->procesosFactory
                ->createProcesoEnvioEmail();
        } else {
            $this
                ->procesosFactory
                ->createProcesoEnvioEmail($instancia->getFechaEnvio());
        }


        return $this->cambiaFase($instancia, $faseFinalizada);
    }

    public function compruebaPromocionesRechazadasEnFaseCierre(InstanciaComunicacion $instancia)
    {
        if ($instancia->getFase()->getCodigo() !== InstanciaComunicacion::FASE_CIERRE) {
            return false;
        }

        /**
         * Comprueba si hay promociones rechazadas
         *
         * @var NumPromociones $numPro
         * @var Promocion      $promocion
         */
        foreach ($instancia->getNumPromociones() as $numPro) {
            if ($numPro->getTotalPromocionesRechazadas() > 0) {
                return true;
            }
        }

        return false;
    }

} 