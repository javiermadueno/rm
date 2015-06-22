<?php

namespace RM\ComunicacionBundle\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;
use RM\ComunicacionBundle\Entity\SegmentoComunicacion;

class SegmentoComunicacionServicio
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
    }

    public function getSegmentosComunicacion()
    {
        $repo      = $this->em->getRepository('RMComunicacionBundle:SegmentoComunicacion');
        $registros = $repo->obtenerSegmentosComunicacion();

        return $registros;
    }

    public function getSegmentosComunicacionById($id_comunicacion = -1, $id_segmento = -1, $estado = -1)
    {
        $repo      = $this->em->getRepository('RMComunicacionBundle:SegmentoComunicacion');
        $registros = $repo->obtenerSegmentosComunicacionById($id_comunicacion, $id_segmento, $estado);

        return $registros;
    }

    public function guardarObjeto($objeto)
    {
        $this->em->persist($objeto);
        $this->em->flush();

        return $objeto;
    }

    public function deleteSegmentosComunicacion($id_segmento_comunicacion)
    {
        $registros = $this->getSegmentosComunicacionBySC($id_segmento_comunicacion);

        $devolver = 1;
        if ($registros) {
            foreach ($registros as $objSC) {
                $objSC->setEstado(-1);
                $this->em->persist($objSC);
            }

            $this->em->flush();
        } else {
            $devolver = 0;
        }

        return $devolver;
    }

    public function getSegmentosComunicacionBySC($id_segmento_comunicacion)
    {
        $repo      = $this->em->getRepository('RMComunicacionBundle:SegmentoComunicacion');
        $registros = $repo->obtenerSegmentosComunicacionBySC($id_segmento_comunicacion);

        return $registros;
    }

    public function controladorSegmentosComunicacion($id_segmento_comunicacion)
    {
        $registros = $this->getSegmentosComunicacionBySC($id_segmento_comunicacion);

        $devolver = 1;
        if ($registros) {
            foreach ($registros as $objSC) {
                if ($objSC->getEstado() == 1) {
                    $objSC->setEstado(0);
                } elseif ($objSC->getEstado() == 0) {
                    $objSC->setEstado(1);
                }
                $this->em->persist($objSC);
            }

            $this->em->flush();
        } else {
            $devolver = 0;
        }

        return $devolver;
    }

    public function getNuevosSegmentosParaComunicacion($id_comunicacion)
    {

        $repo      = $this->em->getRepository('RMComunicacionBundle:SegmentoComunicacion');
        $registros = $repo->obtenerNuevosSegmentosParaComunicacion($id_comunicacion);

        return $registros;

    }

    public function guardarNuevoSegmentoComunicacion(
        $id_segmento_comunicacion = -1,
        $id_comunicacion,
        $id_segmento,
        $fecha_inicio,
        $fecha_fin,
        $estado,
        $tipo,
        $mes,
        $dia,
        $hora
    ) {

        //$my_date = date('m/d/y', strtotime($date));
        $repo = $this->em->getRepository('RMComunicacionBundle:SegmentoComunicacion');

        $repoSeg = $this->em->getRepository('RMSegmentoBundle:Segmento');
        $repoCom = $this->em->getRepository('RMComunicacionBundle:Comunicacion');

        $regSegmentos = $repoSeg->obtenerSegmentoById($id_segmento);
        $idSegmento   = $regSegmentos[0];

        $regComunicaciones = $repoCom->obtenerComunicacionById($id_comunicacion);
        $idComunicacion    = $regComunicaciones[0];
        if ($id_segmento_comunicacion == -1) {
            $objSegCom = new SegmentoComunicacion();
        } else {
            $regSegmentosCom = $repo->obtenerSegmentosComunicacionById($id_comunicacion, $id_segmento);
            $objSegCom       = $regSegmentosCom[0];
        }
        $objSegCom->setTipo($tipo);
        $objSegCom->setEstado($estado);

        $fechaInicio = \DateTime::createFromFormat('d/m/Y H:i', sprintf("%s %s", $fecha_inicio, $hora));

        $fechaFin = \DateTime::createFromFormat('d/m/Y', $fecha_fin);
        $objSegCom->setFecInicio($fechaInicio);
        $objSegCom->setFecFin($fechaFin);

        $objHora = \DateTime::createFromFormat('H:i', $hora);
        $objSegCom->setHoraProg($objHora);

        $objSegCom->setIdComunicacion($regComunicaciones[0]);
        $objSegCom->setIdSegmento($regSegmentos[0]);

        if ($tipo > 1) {
            $objSegCom->setDia($dia);
            if ($tipo > 3) {
                $objSegCom->setMes($mes);
            }
        }

        $objSegCom->getProximaEjecucion();

        $this->em->merge($objSegCom);
        $this->em->flush();

        return 1;

    }


}