<?php

namespace RM\ComunicacionBundle\Entity;

use Doctrine\ORM\EntityRepository;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\ProductoBundle\Entity\Promocion;

class CreatividadRepository extends EntityRepository
{
    /**
     * @return Creatividad[]
     */
    public function findAll()
    {
        $creatividades = $this->_em->createQueryBuilder('c')
            ->where('c.estado > -1')
            ->orderBy('c.idCreatividad')
            ->getQuery()
            ->getResult();

        return $creatividades;
    }

    public function obtenerCreatividadById($idCreatividad)
    {


        $dql = "SELECT c
		FROM RMComunicacionBundle:Creatividad c
		WHERE c.idCreatividad = :id_creatividad
		AND c.estado > -1";


        $query    = $this->_em
            ->createQuery($dql)
            ->setParameter('id_creatividad', $idCreatividad)
        ;
        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerCreatividadByFiltro($nombre)
    {

        $result = $this->obtenerCreatividadByFiltroDQL($nombre);

        return $result;
    }

    public function obtenerCreatividadByFiltroDQL($nombre)
    {

        $qb = $this->createQueryBuilder('c')
            ->where('c.estado > -1')
            ->orderBy('c.idCreatividad', 'ASC')
        ;

        if (!empty($nombre)) {
            $qb
                ->andWhere('c.nombre like :nombre')
                ->setParameter('nombre', sprintf("%%%s%%", $nombre))
            ;
        }

       $registro = $qb->getQuery()->getResult();

        return $registro;
    }


    public function obtenerPromocionesCreatividad($idInstancia)
    {

        $dql = "
            SELECT p
            FROM RMComunicacionBundle:InstanciaComunicacion ic
            JOIN RMProductoBundle:NumPromociones np WITH (ic.idInstancia = np.idInstancia AND np.estado > -1)
            JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo AND gs.estado > -1 AND gs.tipo = :tipo)
            JOIN RMProductoBundle:Promocion p WITH (np.idNumPro = p.numPromocion AND p.estado > -1)
            WHERE ic.estado > -1
            AND   ic.idInstancia = :idInstancia
            ORDER BY ic.idInstancia
            ";

        $query = $this->_em->createQuery($dql)
            ->setParameter('tipo', GrupoSlots::CREATIVIDADES)
            ->setParameter('idInstancia', $idInstancia);

        return $query->getResult();
    }

    public function obtenerGrupoSlotsNumPromocionesCreatividad($idInstancia)
    {


        $dql = "
             SELECT g.idGrupo, g.nombre, np.numSegmentadas, np.numGenericas,
                COUNT(DISTINCT pcs.idPromocion) AS promCreatividadSegmentadas,
                COUNT(DISTINCT pcg.idPromocion) AS promCreatividadGenericas,
                GROUP_CONCAT(DISTINCT pcs.idPromocion) AS idsPromoCrSegmentadas,
                GROUP_CONCAT(DISTINCT pcg.idPromocion) AS idsPromoCrGenericas
             FROM 	RMPlantillaBundle:GrupoSlots g
             JOIN	RMProductoBundle:NumPromociones np WITH (g.idGrupo = np.idGrupo AND np.estado > -1 AND np.idInstancia = :idInstancia)
             LEFT JOIN	RMProductoBundle:Promocion pcs WITH (np.idNumPro = pcs.numPromocion AND pcs.estado > -1 AND pcs.tipo = :segmentada)
             LEFT JOIN	RMProductoBundle:Promocion pcg WITH (np.idNumPro = pcg.numPromocion AND pcg.estado > -1 AND pcg.tipo = :generica)
             WHERE  g.estado > -1
             AND 	g.tipo = :creatividades
             GROUP BY g.idGrupo
             ORDER BY g.nombre";

        $query = $this->_em->createQuery($dql)
            ->setParameters([
                'idInstancia'   => $idInstancia,
                'segmentada'    => Promocion::TIPO_SEGMENTADA,
                'generica'      => Promocion::TIPO_GENERICA,
                'creatividades' => GrupoSlots::CREATIVIDADES
            ]);

        return $query->getResult();
    }

}
