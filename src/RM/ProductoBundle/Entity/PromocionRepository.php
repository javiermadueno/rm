<?php

namespace RM\ProductoBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityRepository;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;

class PromocionRepository extends EntityRepository
{
    /**
     * @param $id
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBydId($id)
    {
        return $this->createQueryBuilder('p')
            ->join('p.idProducto', 'producto')
            ->where('p.estado > -1')
            ->andWhere('p.idPromocion = :id')
            ->setParameter('id', $id)
            ->getQuery()->getSingleResult();
    }

    public function obtenerPromocionesCampanya($id_categoria, $id_instancia, $tipo)
    {

        $dql = "select p
		from RMProductoBundle:Promocion p
		JOIN RMProductoBundle:NumPromociones np with(np.idInstancia = :id_instancia and p.numPromocion = np.idNumPro)
		WHERE p.estado > -1
		AND   p.tipo = :tipo
		";

        if ($id_categoria !== null && $id_categoria > 0) {
            $dql .= "AND   np.idCategoria = :id_categoria ";
        }

        $dql .= "ORDER BY np.idGrupo, p.idProducto ";

        $query = $this->_em->createQuery($dql);

        $query->setParameter('id_instancia', $id_instancia);

        if ($id_categoria !== null && $id_categoria > 0) {
            $query->setParameter('id_categoria', $id_categoria);
        }

        $query->setParameter('tipo', $tipo);
        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerPromocionesByIdInstancia($id_instancia)
    {

        $dql = "select p
		from RMProductoBundle:Promocion p
		WHERE p.estado > -1
		AND   p.idInstancia = :id_instancia";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_instancia', $id_instancia);
        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerPromocionesCierreCampanya($id_categoria, $id_instancia)
    {

        $dql = "SELECT p
                FROM RMProductoBunlde:Promocion p
                JOIN RMComunicacionBundle:InstanciaComunicacion ic on (ic.IdInstanciaComunicacion = p.idIntanciaComunicacion)
                WHERE p.estado > -1
                AND ic.idInstanciaComunicacion = :id_instancia
                AND ic.fase = :fase";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_instancia', $id_instancia);
        $query->setParameter('fase', 7);

        return $query->getResult();
    }

    public function obtenerPromocionAsignadaSlot($id_slot, $id_plantilla)
    {

        $dql = "select icc
		from RMProductoBundle:Promocion p
		JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH (p.idInstancia = ic.idInstancia ANd ic.estado = 1  )
		JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (ic.idSegmentoComunicacion = sc.idSegmentoComunicacion AND sc.estado > -1)
		JOIN RMComunicacionBundle:Comunicacion c WITH (sc.idComunicacion = c.idComunicacion AND c.estado > -1)
		JOIN RMPlantillaBundle:Plantilla pl WITH (c.idComunicacion = pl.idComunicacion AND pl.estado > -1 AND pl.idPlantilla = :idplantilla)
		JOIN RMSegmentoBundle:Segmento s WITH (sc.idSegmento = s.idSegmento AND s.estado > -1)
		JOIN RMClienteBundle:ClienteSegmento cs WITH (s.idSegmento = cs.idSegmento)
		JOIN RMClienteBundle:Cliente cli WITH (cs.idCliente = cli.idCliente ANd cli.estado > -1)
		JOIN RMProductoBundle:SegmentoPromocion sp WITH (s.idSegmento = sp.idSegmento AND p.idPromocion = sp.idPromocion)
		JOIN RMClienteBundle:InstanciaComunicacionCliente icc WITH (cli.idCliente = icc.idCliente AND icc.idSlot = :idslot AND p.idPromocion = icc.idPromocion AND ic.idInstancia = icc.idInstancia)
		WHERE p.estado > -1
		GROUP BY p.idPromocion";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idslot', $id_slot);
        $query->setParameter('idplantilla', $id_plantilla);
        $registro = $query->getResult();

        return $registro;
    }



    // Le pasamos un array con los datos para insertar

    public function obtenerPromocionById($id_promocion)
    {



        $dql = "select p
		from RMProductoBundle:Promocion p
		WHERE p.estado > -1
		AND   p.idPromocion = :id_promocion";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_promocion', $id_promocion);
        $registro = $query->getResult();

        return $registro;
    }

    public function actualizarPromocionesCampanya($promoToUpdateData)
    {
        $em = $this->_em;

        $promocion = $this->find($promoToUpdateData ['id_promocion']);

        if (!$promocion instanceof Promocion) {
            return 0;
        }

        $promocion
            ->setAceptada($promoToUpdateData['estado']);

        $em->persist($promocion);
        $em->flush($promocion);

        return 1;
    }

    /**
     * @param null     $id_categoria
     * @param null     $id_producto
     * @param null     $id_marca
     * @param DateTime $fechaInicio
     * @param DateTime $fechaFin
     *
     * @return array
     */
    public function obtenerPromocionesFiltradasPor
    (
        $id_categoria = null,
        $id_producto = null,
        $id_marca = null,
        \DateTime $fechaInicio = null,
        \DateTime $fechaFin = null
    ) {

        $qb = $this->_em->createQueryBuilder()
            ->select('p.simulado as simulado',
              'c.nombre as nombreComunicacion',
              'pr.nombre as nombreProducto',
              'm.nombre as nombreMarca',
              'tp.nombre as tipoPromocion',
              'ic.fecCreacion as fecha',
              'p.aceptada as aceptada',
              'cat.idCategoria as idCategoria',
              'm.idMarca as idMarca')
            ->from('RMProductoBundle:Promocion', 'p')
            ->join('p.idTipoPromocion', 'tp')
            ->join('p.idProducto', 'pr')
            ->join('pr.idMarca', 'm')
            ->join('p.numPromocion', 'np')
            ->join('np.idCategoria', 'cat')
            ->join('np.idInstancia', 'ic')
            ->join('ic.idSegmentoComunicacion', 'sc')
            ->join('sc.idComunicacion', 'c')
            ->join('ic.fase', 'fase')
            ->where('p.estado > -1')
            ->andWhere('fase.codigo = :fase_confirmacion')
            ->groupBy('p.idPromocion')
            ->orderBy('p.idPromocion', 'ASC')
            ->setParameter('fase_confirmacion', InstanciaComunicacion::FASE_CONFIRMACION)
        ;

        if ($id_categoria > 0) {
            $qb->andWhere('np.idCategoria = :categoria')
                ->setParameter('categoria', $id_categoria);
        }

        if ($id_producto > 0) {
            $qb->andWhere('pr.idProducto = :producto')
                ->setParameter('producto', $id_producto);
        }

        if ($id_marca > 0) {
            $qb->andWhere('m.idMarca = :marca')
                ->setParameter('marca', $id_marca);
        }

        if ($fechaInicio) {
            $qb->andWhere('ic.fecCreacion >= :fecha_inicio')
                ->setParameter('fecha_inicio', $fechaInicio);
        }

        if ($fechaFin) {
            $qb->andWhere('ic.fecCreacion <= :fecha_fin')
                ->setParameter('fecha_fin', $fechaFin);
        }

        return $qb->getQuery()->getResult();

    }

}