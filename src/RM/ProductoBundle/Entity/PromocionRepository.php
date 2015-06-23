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

        $em = $GLOBALS ['kernel']->getContainer()->get('doctrine')->getManager($_SESSION ['connection']);

        $dql = "select p
		from RMProductoBundle:Promocion p
		JOIN RMProductoBundle:NumPromociones np with(np.idInstancia = :id_instancia and p.numPromocion = np.idNumPro)
		WHERE p.estado > -1
		AND   p.tipo = :tipo
		";

        if ($id_categoria != null && $id_categoria > 0) {
            $dql .= "AND   np.idCategoria = :id_categoria ";
        }

        $dql .= "ORDER BY np.idGrupo, p.idProducto ";

        $query = $em->createQuery($dql);

        $query->setParameter('id_instancia', $id_instancia);

        if ($id_categoria != null && $id_categoria > 0) {
            $query->setParameter('id_categoria', $id_categoria);
        }

        $query->setParameter('tipo', $tipo);
        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerPromocionesByIdInstancia($id_instancia)
    {

        // $em = $this->getEntityManager();
        $em = $GLOBALS ['kernel']->getContainer()->get('doctrine')->getManager($_SESSION ['connection']);

        $dql = "select p
		from RMProductoBundle:Promocion p
		WHERE p.estado > -1
		AND   p.idInstancia = :id_instancia";

        $query = $em->createQuery($dql);
        $query->setParameter('id_instancia', $id_instancia);
        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerPromocionesCierreCampanya($id_categoria, $id_instancia)
    {
        $em = $GLOBALS ['kernel']->getContainer()->get('doctrine')->getManager($_SESSION ['connection']);

        $dql = "SELECT p
                FROM RMProductoBunlde:Promocion p
                JOIN RMComunicacionBundle:InstanciaComunicacion ic on (ic.IdInstanciaComunicacion = p.idIntanciaComunicacion)
                WHERE p.estado > -1
                AND ic.idInstanciaComunicacion = :id_instancia
                AND ic.fase = :fase";

        $query = $em->createQuery($dql);
        $query->setParameter('id_instancia', $id_instancia);
        $query->setParameter('fase', 7);

        return $query->getResult();
    }

    public function obtenerPromocionAsignadaSlot($id_slot, $id_plantilla)
    {
        // $em = $this->getEntityManager();
        $em = $GLOBALS ['kernel']->getContainer()->get('doctrine')->getManager($_SESSION ['connection']);

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

        $query = $em->createQuery($dql);
        $query->setParameter('idslot', $id_slot);
        $query->setParameter('idplantilla', $id_plantilla);
        $registro = $query->getResult();

        return $registro;
    }

    public function guardarPromocionesCampanya($promoToSaveData)
    {
        $em = $GLOBALS ['kernel']->getContainer()->get('doctrine')->getManager($_SESSION ['connection']);

        if ($promoToSaveData ['idPromo'] > 0) {

            $promocion = $this->obtenerPromocionById($promoToSaveData ['idPromocion']);
            $promo = $promocion[0];

        } else {
            $promo = new Promocion();
        }

        $promo->setIdProducto($em->getReference('RMProductoBundle:Producto', $promoToSaveData['producto']))
            ->setTipo($promoToSaveData ['tipo'])
            ->setMinimo($promoToSaveData ['minimo'])
            ->setIdTipoPromocion($em->getReference('RMProductoBundle:TipoPromocion', $promoToSaveData ['tipo']))
            ->setEstado(1);

        $em->persist($promo);
        $em->flush();

        return 1;
    }

    // Le pasamos un array con los datos para insertar

    public function obtenerPromocionById($id_promocion)
    {

        $em = $GLOBALS ['kernel']->getContainer()->get('doctrine')->getManager($_SESSION ['connection']);

        $dql = "select p
		from RMProductoBundle:Promocion p
		WHERE p.estado > -1
		AND   p.idPromocion = :id_promocion";

        $query = $em->createQuery($dql);
        $query->setParameter('id_promocion', $id_promocion);
        $registro = $query->getResult();

        return $registro;
    }

    public function actualizarPromocionesCampanya($promoToUpdateData)
    {
        // $em = $this->getEntityManager();
        $em = $GLOBALS ['kernel']->getContainer()->get('doctrine')->getManager($_SESSION ['connection']);

        $repo = $em->getRepository('RMProductoBundle:Promocion');

        $promocion = $repo->find($promoToUpdateData ['id_promocion']);
        if ($promocion) {
            $promocion->setAceptada($promoToUpdateData['estado']);
            $em->persist($promocion);
            $em->flush();

            return 1;
        } else {
            return 0;
        }

    }

    public function obtenerPromocionesFiltradasPor
    (
        $id_categoria = null,
        $id_producto = null,
        $id_marca = null,
        \DateTime $fechaInicio = null,
        \DateTime $fechaFin = null
    ) {


        $dql = "SELECT DISTINCT  p.idPromocion as id,
              p.simulado as simulado,
              c.nombre as nombreComunicacion,
              pr.nombre as nombreProducto,
              m.nombre as nombreMarca,
              tp.nombre as tipoPromocion,
              ic.fecCreacion as fecha,
              p.aceptada as aceptada,
              cat.idCategoria as idCategoria,
              m.idMarca as idMarca
            FROM RMProductoBundle:Promocion p
            JOIN p.idTipoPromocion tp
            JOIN p.idProducto pr
            JOIN pr.idMarca m
            JOIN p.numPromocion np
            join np.idCategoria cat
            JOIN np.idInstancia ic
            JOIN ic.idSegmentoComunicacion sc
            JOIN sc.idComunicacion c
            JOIN  ic.fase as fase
            WHERE
              p.estado > -1
              AND fase.codigo = '" . InstanciaComunicacion::FASE_CONFIRMACION . "'
            ";
        //Esto habrá que añadirlo mas adelante para que busque solo las promociones de las Instancias de comunicacion
        //en fase generación
        //JOIN  ic.fase as fase WITH (fase.codigo = '".InstanciaComunicacion::FASE_GENERACION."')

        if ($id_categoria > 0) {
            $dql .= "AND np.idCategoria = " . $id_categoria;
        }

        if ($id_producto > 0) {
            $dql .= "AND pr.idProducto = " . $id_producto;
        }

        if ($id_marca > 0) {
            $dql .= "AND m.idMarca = " . $id_marca;
        }

        if ($fechaInicio) {
            $dql .= "AND ic.fecCreacion >= '" . $fechaInicio->format("Y-m-d") . "'";
        }

        if ($fechaFin) {
            $dql .= "AND ic.fecCreacion <= '" . $fechaFin->format("Y-m-d") . "'";
        }

        $dql .= " GROUP BY p.idPromocion ORDER BY p.idPromocion";


        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }

    public function findPromocionesAsignadasASlot($idSlot, $idPlantilla, $idInstancia)
    {
        $dql = "
            SELECT p
            FROM RMProductoBundle:Promocion p
            JOIN p.idNumPro as np WITH (np.idInstancia = :id_instancia AND np.estado > -1)


        ";
    }
}