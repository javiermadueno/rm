<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\EntityRepository;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\Promocion;

class InstanciaComunicacionRepository extends EntityRepository
{

    public function obtenerInstanciaById($id_instancia)
    {

        $dql = "select ic
			from RMComunicacionBundle:InstanciaComunicacion ic
			WHERE ic.idInstancia = :idinstancia
			AND ic.estado > -1";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idinstancia', $id_instancia);

        $registros = $query->getResult();

        return $registros;

    }

    /**
     * @param $id_instancia
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id_instancia)
    {
        $instancia = $this->createQueryBuilder('i')
            ->where('i.idInstancia = :id_instancia')
            ->andWhere('i.estado > -1')
            ->setParameter('id_instancia', $id_instancia)
            ->getQuery()
            ->getOneOrNullResult();

        return $instancia;

    }

    public function obtenerInstanciasByFiltroDQL($id_comunicacion, $id_segmento, $fase, $fecha_inicio, $fecha_fin)
    {

        $dql = "SELECT ic
		FROM RMComunicacionBundle:InstanciaComunicacion ic
		JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (ic.idSegmentoComunicacion = sc.idSegmentoComunicacion)
		JOIN RMComunicacionBundle:Comunicacion c WITH (sc.idComunicacion = c.idComunicacion)
		JOIN RMSegmentoBundle:Segmento s WITH (sc.idSegmento = s.idSegmento)
		WHERE ic.estado > -1";

        if ($id_comunicacion != -1) {
            $dql .= " AND c.idComunicacion IN (" . $id_comunicacion . ")";
        }

        if ($id_segmento != -1) {
            $dql .= " AND s.idSegmento IN (" . $id_segmento . ")";
        }

        if ($fase != -1) {
            $dql .= " AND ic.fase IN (" . $fase . ")";
        }

        if ($fecha_inicio != -1 && $fecha_inicio != '') {

            $fecha_init = new \DateTime($fecha_inicio);
            $fecha_inicial = $fecha_init->format('Y-m-d H:i:s');

            if ($fecha_fin != -1 && $fecha_fin != '') {
                $fecha_end = new \DateTime($fecha_fin);
                $fecha_final = $fecha_end->format('Y-m-d H:i:s');
                $dql .= " AND ic.fecEjecucion > '" . $fecha_inicial . "' AND ic.fecEjecucion < '" . $fecha_final . "'";
            } else {
                $dql .= " AND ic.fecEjecucion > '" . $fecha_inicial . "'";
            }
        } elseif ($fecha_fin != -1 && $fecha_fin != '') {
            $fecha_end = new \DateTime($fecha_fin);
            $fecha_final = $fecha_end->format('Y-m-d H:i:s');
            $dql .= " AND ic.fecEjecucion < '" . $fecha_final . "'";
        }

        $dql .= " ORDER BY ic.idInstancia ASC";

        $query = $this->_em->createQuery($dql);

        return $query;
    }

    public function obtenerInstanciasByFiltro($id_comunicacion, $id_segmento, $fase, $fecha_inicio, $fecha_fin)
    {

        return $this->obtenerInstanciasByFiltroDQL($id_comunicacion, $id_segmento, $fase, $fecha_inicio,
            $fecha_fin)->getResult();
    }

    public function obtenerResumenPromocionesByTipo($id_instancia)
    {

        $dql = "
          SELECT gs.idGrupo, gs.nombre AS nombreGrupo,
                 cat.idCategoria, cat.nombre AS nombreCategoria,
                 np.numSegmentadas, np.numGenericas,
                 COUNT(DISTINCT pSeg.idPromocion) As num_pro_seg,
                 COUNT(DISTINCT pGen.idPromocion) As num_pro_gen,
                 gs.tipo as tipoGrupo
		    FROM RMComunicacionBundle:InstanciaComunicacion ic
		    JOIN RMProductoBundle:NumPromociones np WITH (ic.idInstancia = np.idInstancia AND np.estado > -1)
		    JOIN RMPlantillaBundle:GrupoSlots AS gs WITH (np.idGrupo = gs.idGrupo AND gs.estado > -1)
       LEFT JOIN RMCategoriaBundle:Categoria AS cat WITH (np.idCategoria = cat.idCategoria)
       LEFT JOIN RMProductoBundle:Promocion pSeg WITH ( np = pSeg.numPromocion AND pSeg.tipo = 0 AND pSeg.estado > -1)
       LEFT JOIN RMProductoBundle:Promocion pGen WITH ( np = pGen.numPromocion AND pGen.tipo = 1 AND pGen.estado > -1)
           WHERE ic.estado > -1
		     AND ic.idInstancia = :idinstancia
		GROUP BY np.idGrupo, np.idCategoria
		ORDER BY gs.nombre
		";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idinstancia', $id_instancia);

        return $query->getResult();
    }

    public function obtenerResumenPromocionesByEstado($id_instancia)
    {
        $dql = "
                SELECT gs.idGrupo, cat.idCategoria,
				SUM(CASE WHEN pro.aceptada = :aceptada THEN 1 ELSE 0 END) AS aceptadas,
				SUM(CASE WHEN pro.aceptada = :pendiente THEN 1 ELSE 0 END) AS pendientes,
				SUM(CASE WHEN pro.aceptada = :rechazada THEN 1 ELSE 0 END) AS rechazadas,
				gs.tipo as tipoGrupo,
				gs.nombre as nombreGrupo,
				np.idNumPro as idNumPro
				FROM RMComunicacionBundle:InstanciaComunicacion ic
				JOIN RMProductoBundle:NumPromociones np WITH (np.idInstancia = ic.idInstancia)
				JOIN RMProductoBundle:Promocion pro WITH (np = pro.numPromocion AND pro.aceptada IN (:tipos) AND pro.tipo=:segmentada AND pro.estado > -1)
				JOIN RMPlantillaBundle:GrupoSlots AS gs WITH (np.idGrupo = gs.idGrupo AND gs.estado > -1)
				JOIN RMCategoriaBundle:Categoria AS cat WITH (np.idCategoria = cat.idCategoria)
				WHERE ic.estado > -1
				AND ic.idInstancia = :idinstancia
				GROUP BY np.idGrupo, np.idCategoria
				";

        $query = $this->_em->createQuery($dql);
        $query
            ->setParameter('idinstancia', $id_instancia)
            ->setParameter('aceptada', Promocion::ACEPTADA)
            ->setParameter('pendiente', Promocion::PENDIENTE)
            ->setParameter('rechazada', Promocion::RECHAZADA)
            ->setParameter('segmentada', Promocion::TIPO_SEGMENTADA)
            ->setParameter('tipos', [
                Promocion::ACEPTADA,
                Promocion::PENDIENTE,
                Promocion::RECHAZADA
            ]);

        return $query->getResult();
    }

    public function obtenerCampanyasByFiltro($id_categoria)
    {
        $qb = $this->createQueryBuilder('ic')
            ->join('RMProductoBundle:NumPromociones', 'np', 'WITH',
                'ic.idInstancia = np.idInstancia AND np.estado > -1')
            ->join('RMComunicacionBundle:Fases', 'f', 'WITH', 'f.codigo = :codigo AND ic.fase = f')
            ->where('ic.estado > -1')
            ->groupBy('ic.idInstancia')
            ->orderBy('ic.idInstancia', 'ASC')
            ->setParameter('codigo', InstanciaComunicacion::FASE_NEGOCIACION);

        if ($id_categoria != null && $id_categoria > 0) {
            $qb->andWhere('np.idCategoria IN (:categoria)')
                ->setParameter('categoria', $id_categoria);
        }

        return $qb->getQuery()->getResult();
    }

    public function obtenerClosingCampaigns()
    {


        $dql = "
          SELECT ic
          FROM RMComunicacionBundle:InstanciaComunicacion ic
          JOIN RMComunicacionBundle:Fases fase WITH(fase.codigo = :codigo and ic.fase = fase)
		  WHERE ic.estado > -1
		  GROUP BY ic.idInstancia
          ORDER BY ic.idInstancia ASC
		";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('codigo', InstanciaComunicacion::FASE_CIERRE);
        return $query->getResult();
    }

    public function obtenerClosingCampaignsByFiltro($id_categoria)
    {

        $qb = $this->createQueryBuilder('ic')
            ->join('RMComunicacionBundle:SegmentoComunicacion', 'sc', 'WITH',
                'ic.idSegmentoComunicacion = sc.idSegmentoComunicacion')
            ->join('RMComunicacionBundle:Comunicacion', 'c', 'WITH', 'sc.idComunicacion = c.idComunicacion')
            ->join('RMSegmentoBundle:Segmento', 's', 'WITH', 'sc.idSegmento = s.idSegmento')
            ->join('RMProductoBundle:NumPromociones', 'np', 'WITH',
                'ic.idInstancia = np.idInstancia AND np.estado > -1')
            ->join('ic.fase', 'f')
            ->where('ic.estado > -1')
            ->andWhere('f.codigo = :codigo')
            ->groupBy('ic.idInstancia')
            ->orderBy('ic.idInstancia', 'ASC')
            ->setParameter('codigo', InstanciaComunicacion::FASE_CIERRE);

        if ($id_categoria != null && $id_categoria > 0) {
            $qb->andWhere('np.idCategoria = :categoria')
                ->setParameter('categoria', $id_categoria);
        }

        return $qb->getQuery()->getResult();
        /**
         * $dql = "SELECT ic
         * FROM RMComunicacionBundle:InstanciaComunicacion ic
         * JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (ic.idSegmentoComunicacion = sc.idSegmentoComunicacion)
         * JOIN RMComunicacionBundle:Comunicacion c WITH (sc.idComunicacion = c.idComunicacion)
         * JOIN RMSegmentoBundle:Segmento s WITH (sc.idSegmento = s.idSegmento)
         * JOIN RMProductoBundle:NumPromociones np WITH (ic.idInstancia = np.idInstancia AND np.estado > -1)
         * WHERE ic.estado > 0
         * AND  ic.fase = 3";
         *
         * if($id_categoria != null && $id_categoria > 0){
         * $dql .= " AND np.idCategoria IN (". $id_categoria. ")";
         * }
         *
         * $dql .= " GROUP BY ic.idInstancia
         * ORDER BY ic.idInstancia ASC";
         *
         * $query = $this->_em->createQuery($dql);
         *
         * return $query->getResult();**/
    }

    public function obtenerInstanciasCreatividad()
    {
        $dql = "SELECT ic
            FROM RMComunicacionBundle:InstanciaComunicacion ic
            JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (sc.idSegmentoComunicacion = ic.idSegmentoComunicacion )
            JOIN RMComunicacionBundle:Comunicacion com WITH (com.idComunicacion = sc.idComunicacion)
            JOIN RMSegmentoBundle:Segmento seg WITH (sc.idSegmento = seg.idSegmento)
            JOIN RMPlantillaBundle:Plantilla p WITH (com.plantilla = p.idPlantilla AND p.estado> -1)
    		JOIN RMProductoBundle:NumPromociones np WITH (ic.idInstancia = np.idInstancia AND np.estado > -1)
            JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo AND gs.estado > -1 AND gs.tipo = :tipo)
            WHERE ic.estado > -1
            ORDER BY ic.idInstancia
            ";

        $query = $this->_em->createQuery($dql)
            ->setParameter('tipo', GrupoSlots::CREATIVIDADES);

        return $query->getResult();
    }

}