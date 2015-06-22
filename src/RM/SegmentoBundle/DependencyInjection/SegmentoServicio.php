<?php

namespace RM\SegmentoBundle\DependencyInjection;

use Doctrine\Common\Persistence\ManagerRegistry;
use RM\DiscretasBundle\Entity\Tipo;
use RM\DiscretasBundle\Entity\Vid;
use RM\DiscretasBundle\Entity\VidGrupoSegmento;
use RM\DiscretasBundle\Entity\VidSegmento;


class SegmentoServicio
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager($_SESSION['connection']);
    }

    public function getSegmentoById($id_segmento)
    {
        $registros = $this->em->getRepository('RMSegmentoBundle:Segmento')->obtenerSegmentoById($id_segmento);

        return $registros;

    }

    public function getSegmentoByIdComunicacion($id_comunicacion)
    {
        $registros = $this->em->getRepository('RMSegmentoBundle:Segmento')->obtenerSegmentoByIdComunicacion($id_comunicacion);

        return $registros;

    }

    public function getSegmentosFiltrados(
        $tipo,
        $id_categoria = -1,
        $id_marca = -1,
        $id_proveedor = -1,
        $id_variable = -1,
        $fecha = null
    ) {

        $repo = $this->em->getRepository('RMSegmentoBundle:Segmento');

        $id_tipo = $tipo instanceof Tipo ? $tipo->getId() : -1;
        $codigo = $tipo instanceof Tipo ? $tipo->getCodigo() : -1;

        switch ($codigo) {
            case Tipo::COMPRA_PRODUCTO:
                $registros = $repo
                    ->findSegmentosCompraProductoByIdVariableCategoriaMarca($id_variable, $id_categoria, $id_marca,
                        $id_proveedor, $fecha);
                break;
            case Tipo::RFM:
                $registros = $repo->findSegmentosRFM($id_variable, $fecha);
                break;
            case Tipo::HABITOS_COMPRA:
                $registros = $repo->findSegmentosHabitosCompra($id_variable, $fecha);
                break;
            case Tipo::SOCIODEMOGRAFICO:
                $registros = $repo->findSegmentosSocioDemograficos($id_variable, $fecha);
                break;
            case Tipo::CICLO_VIDA:
                $registros = $repo->findSegmentosCicloVida($id_variable, $fecha);
                break;
            default:
                $registros = $repo->obtenerSegmentosFiltrados($id_tipo, $id_categoria, $id_marca, $fecha);
        }

        return $registros;
    }

    public function getSegmentosInstancia($idSegmento)
    {

        $repo = $this->em->getRepository('RMSegmentoBundle:Segmento');
        $registros = $repo->obtenerSegmentosByInstancia($idSegmento);

        return $registros;
    }


    public function getSegmentosByIdVidSegmento($id_variable)
    {
        $vid = $this->em->find('RMDiscretasBundle:Vid', $id_variable);

        if (!$vid instanceof Vid) {
            return [];
        }

        $repo = $this->em->getRepository('RMDiscretasBundle:Vid');
        $grupo = $repo->obtenerUnicoGrupoSegmentoByVid($vid->getIdVid());

        if (!$grupo instanceof VidGrupoSegmento) {
            return [];
        }

        $segmentos = $repo->obtenerSegmentosByIdGrupo($grupo->getIdVidGrupoSegmento());

        $idsSegmentos = array_map(
            function (VidSegmento $segmento) {
                return $segmento->getIdVidSegmento();
            }, $segmentos);

        return $this->em->getRepository('RMSegmentoBundle:Segmento')
            ->createQueryBuilder('s')
            ->where('s.idVidSegmento IN (:ids) ')
            ->setParameter('ids', $idsSegmentos)
            ->getQuery()
            ->getResult();
    }

}