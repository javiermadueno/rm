<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\EntityRepository;
use RM\InsightBundle\Filter\InstanciaComunicacionFilter;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\Promocion;

class InstanciaComunicacionRepository extends EntityRepository
{

    /**
     * @param $id_instancia
     *
     * @return InstanciaComunicacion[]
     */
    public function obtenerInstanciaById($id_instancia)
    {
        return $this
            ->createQueryBuilder('i')
            ->where('i.idInstancia = :id')
            ->andWhere('i.estado > -1')
            ->setParameter('id', $id_instancia)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id_instancia
     *
     * @return InstanciaComunicacion
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id_instancia)
    {
        $instancia = $this
            ->createQueryBuilder('i')
            ->addSelect('segmento', 'fase', 'comunicacion', 'np', 'promociones', 'producto', 'tipo')
            ->join('i.idSegmentoComunicacion', 'segmento')
            ->join('i.fase', 'fase')
            ->join('segmento.idComunicacion', 'comunicacion')
            ->leftJoin('i.numPromociones', 'np')
            ->leftJoin('np.promociones', 'promociones')
            ->leftJoin('promociones.idTipoPromocion', 'tipo')
            ->leftJoin('promociones.idProducto', 'producto')
            ->where('i.idInstancia = :id_instancia')
            ->andWhere('i.estado > -1')
            ->setParameter('id_instancia', $id_instancia)
            ->getQuery()
            ->getOneOrNullResult();

        return $instancia;

    }

    /**
     * @param $id_comunicacion
     * @param $id_segmento
     * @param $fase
     * @param $fecha_inicio
     * @param $fecha_fin
     *
     * @return InstanciaComunicacion[]
     */
    public function obtenerInstanciasByFiltro($id_comunicacion, $id_segmento, $fase, $fecha_inicio, $fecha_fin)
    {
        return $this
            ->obtenerInstanciasByFiltroDQL($id_comunicacion, $id_segmento, $fase, $fecha_inicio, $fecha_fin)
            ->getResult();
    }

    /**
     * @param $id_comunicacion
     * @param $id_segmento
     * @param $fase
     * @param $fecha_inicio
     * @param $fecha_fin
     *
     * @return \Doctrine\ORM\Query
     */
    public function obtenerInstanciasByFiltroDQL($id_comunicacion, $id_segmento, $fase, $fecha_inicio, $fecha_fin)
    {
        $qb = $this
            ->createQueryBuilder('ic')
            ->join('ic.idSegmentoComunicacion', 'sc')
            ->join('sc.idComunicacion', 'c')
            ->join('sc.idSegmento', 's')
            ->where('ic.estado > -1')
            ->orderBy('ic.idInstancia', 'ASC');

        if ($id_comunicacion != -1) {
            $qb->andWhere('c.idComunicacion = :id_comunicacion')
               ->setParameter('id_comunicacion', $id_comunicacion);
        }

        if ($id_segmento != -1) {
            $qb->andWhere('s.idSegmento = :id_segmento')
               ->setParameter('id_segmento', $id_segmento);
        }

        if ($fase != -1) {
            $qb->andWhere('ic.fase = :fase')
               ->setParameter('fase', $fase);
        }

        if ($fecha_inicio != -1 && $fecha_inicio != '') {
            $fecha_init = \DateTime::createFromFormat('d/m/Y', $fecha_inicio);
            $qb->andWhere('ic.fecEjecucion >= :fecha_inicio')
               ->setParameter('fecha_inicio', $fecha_init);
        }

        if ($fecha_fin != -1 && $fecha_fin != '') {
            $fecha_end = \DateTime::createFromFormat('d/m/Y', $fecha_fin);
            $qb->andWhere('ic.fecEjecucion <= :fecha_fin')
               ->setParameter('fecha_fin', $fecha_end);
        }

        return $qb->getQuery();

    }

    /**
     * @param InstanciaComunicacionFilter $filter
     *
     * @return InstanciaComunicacion[]
     */
    public function findInstanciasEmailByComunicacionYFechas(InstanciaComunicacionFilter $filter)
    {

        $qb = $this
            ->createQueryBuilder('i')
            ->join('i.idSegmentoComunicacion', 'sc')
            ->join('sc.idComunicacion', 'c')
            ->join('c.idCanal', 'canal')
            ->join('i.fase', 'fase')
            ->where('i.estado > -1')
            ->andwhere('fase.codigo = :finalizada')
            ->andWhere('canal.nombre = :email')
            ->andWhere('i.fechaEnvio is not null')
            ->setParameter('email', 'Email')
            ->setParameter('finalizada', InstanciaComunicacion::FASE_FINALIZADA)
            ->orderBy('i.idInstancia', 'ASC');

        if (null !== $comunicacion = $filter->getComunicacion()) {
            $qb
                ->andWhere('c.idComunicacion = :comunicacion')
                ->setParameter('comunicacion', $comunicacion->getIdComunicacion());
        }

        if (null !== $fechaInicio = $filter->getFechaInicio()) {
            $qb
                ->andWhere('i.fechaEnvio >= :fecha_inicio')
                ->setParameter('fecha_inicio', $fechaInicio);
        }

        if (null !== $fechaFin = $filter->getFechaFin()) {
            $qb
                ->andWhere('i.fechaEnvio <= :fecha_fin')
                ->setParameter('fecha_fin', $fechaFin);
        }

        return $qb->getQuery()->getResult();

    }

    /**
     * @param $id_instancia
     *
     * @return array
     */
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

    /**
     * @param $id_instancia
     *
     * @return array
     */
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

    /**
     * @param $id_categoria
     *
     * @return InstanciaComunicacion[]
     */
    public function obtenerCampanyasByFiltro($id_categoria)
    {
        if (is_array($id_categoria) && empty($id_categoria)) {
            return [];
        }

        $qb = $this
            ->createQueryBuilder('ic')
            ->join('ic.numPromociones', 'np')
            ->join('ic.fase', 'fase')
            ->where('ic.estado > -1')
            ->andWhere('fase.codigo = :codigo')
            ->groupBy('ic.idInstancia')
            ->orderBy('ic.idInstancia', 'ASC')
            ->setParameter('codigo', InstanciaComunicacion::FASE_NEGOCIACION);

        if ($id_categoria !== null && $id_categoria > 0) {
            $qb->andWhere('np.idCategoria IN (:categoria)')
               ->setParameter('categoria', $id_categoria);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return InstanciaComunicacion[]
     */
    public function obtenerClosingCampaigns()
    {
        return $this
            ->createQueryBuilder('i')
            ->join('i.fase', 'fase')
            ->where('i.estado > -1')
            ->andWhere('fase.codigo = :cierre')
            ->orderBy('i.idInstancia', 'ASC')
            ->setParameter('cierre', InstanciaComunicacion::FASE_CIERRE)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id_categoria
     *
     * @return InstanciaComunicacion[]
     */
    public function obtenerClosingCampaignsByFiltro($id_categoria)
    {

        $qb = $this
            ->createQueryBuilder('ic')
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

        if ($id_categoria !== null && $id_categoria > 0) {
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

    /**
     * @return InstanciaComunicacion[]
     */
    public function obtenerInstanciasCreatividad()
    {
        $dql = "SELECT ic
            FROM RMComunicacionBundle:InstanciaComunicacion ic
            JOIN ic.fase as fase
            JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (sc.idSegmentoComunicacion = ic.idSegmentoComunicacion )
            JOIN RMComunicacionBundle:Comunicacion com WITH (com.idComunicacion = sc.idComunicacion)
            JOIN RMSegmentoBundle:Segmento seg WITH (sc.idSegmento = seg.idSegmento)
            JOIN RMPlantillaBundle:Plantilla p WITH (com.plantilla = p.idPlantilla AND p.estado> -1)
    		JOIN RMProductoBundle:NumPromociones np WITH (ic.idInstancia = np.idInstancia AND np.estado > -1)
            JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo AND gs.estado > -1 AND gs.tipo = :tipo)
            WHERE ic.estado > -1
            AND fase.codigo = :codigo_cierre
            ORDER BY ic.idInstancia
            ";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('tipo', GrupoSlots::CREATIVIDADES)
            ->setParameter('codigo_cierre', InstanciaComunicacion::FASE_NEGOCIACION);

        return $query->getResult();
    }

    /**
     * @param $idInstancia
     *
     * @return InstanciaComunicacion[]
     */
    public function findNumRegistrosNumPromocionesPorGrupoSlotsByIdInstancia($idInstancia)
    {
        $dql = "
            SELECT gs.idGrupo as idGrupoSlot , COUNT( DISTINCT np.idNumPro ) as numPro
            FROM RMComunicacionBundle:InstanciaComunicacion ic
            JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (sc.idSegmentoComunicacion = ic.idSegmentoComunicacion AND sc.estado > -1)
            JOIN  RMComunicacionBundle:Comunicacion c WITH (c.idComunicacion = sc.idComunicacion AND c.estado > -1)
            JOIN RMPlantillaBundle:Plantilla p WITH(c.plantilla = p.idPlantilla AND p.estado > -1)
            JOIN RMPlantillaBundle:GrupoSlots  gs WITH (gs.idPlantilla = p.idPlantilla AND gs.estado > -1)
            LEFT JOIN RMProductoBundle:NumPromociones np WITH(np.idGrupo = gs.idGrupo AND np.idInstancia = ic.idInstancia)
            WHERE ic.idInstancia = :idInstancia
            AND ic.estado > -1
            GROUP BY gs.idGrupo
            ORDER BY gs.idGrupo
        ";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idInstancia', $idInstancia);
        $res = $query->getResult();

        return $res;

    }

}