<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use RM\PlantillaBundle\Entity\GrupoSlots;

class NumPromocionesRepository extends EntityRepository
{

    public function obtenerNumPromocionesByFiltros($id_categoria, $id_grupo, $id_instancia)
    {
        $qb = $this->createQueryBuilder('np')
            ->join('np.idInstancia', 'ic')
            ->join('np.idCategoria', 'c')
            ->join('np.idGrupo', 'gs')
            ->where('np.estado > -1')
            ->andWhere('ic.estado > -1')
            ->andWhere('gs.estado > -1')
            ->orderBy('np.idNumPro', 'ASC');

        if ($id_categoria > -1) {
            $qb->andWhere('c.idCategoria = :categoria')
                ->setParameter('categoria', $id_categoria);
        }

        if ($id_grupo > -1) {
            $qb->andWhere('gs.idGrupo = :grupo')
                ->setParameter('grupo', $id_grupo);
        }

        if ($id_instancia > -1) {
            $qb->andWhere('ic.idInstancia = :instancia')
                ->setParameter('instancia', $id_instancia);
        }

        return $qb->getQuery()->getResult();

        /**
         * $dql = "select np
         * from RMProductoBundle:NumPromociones np
         * JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH (np.idInstancia = ic.idInstancia)
         * JOIN RMCategoriaBundle:Categoria c WITH (np.idCategoria = c.idCategoria)
         * JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo)
         * WHERE np.estado > -1
         * AND ic.estado > -1
         * AND gs.estado > -1
         * ";
         *
         * if ($id_categoria > -1) {
         * $dql .= " AND c.idCategoria IN (" . $id_categoria . ")";
         * }
         *
         * if ($id_grupo > -1) {
         * $dql .= " AND gs.idGrupo IN (" . $id_grupo . ")";
         * }
         *
         * if ($id_instancia > -1) {
         * $dql .= " AND ic.idInstancia IN (" . $id_instancia . ")";
         * }
         *
         * $dql .= " ORDER BY np.idNumPro ASC";
         *
         * $query = $this->_em->createQuery($dql);
         * $registro = $query->getResult();
         *
         * return $registro;
         * */

    }

    public function obtenerNumPromocionesCreatividadByFiltros($id_grupo, $id_instancia)
    {

        $dql = "select np
		from RMProductoBundle:NumPromociones np
		JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH (np.idInstancia = ic.idInstancia)
		JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo AND gs.tipo = :tipo_creatividades)
		WHERE np.estado > -1
		AND ic.estado > -1
		AND gs.estado > -1";

        if ($id_grupo > -1) {
            $dql .= " AND gs.idGrupo IN (:grupo)";
        }

        if ($id_instancia > -1) {
            $dql .= " AND ic.idInstancia IN (:instancia)";
        }

        $dql .= " ORDER BY np.idNumPro ASC";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('tipo_creatividades', GrupoSlots::CREATIVIDADES);

        if ($id_grupo > -1) {
            $query->setParameter('grupo', $id_grupo);
        }

        if ($id_instancia > -1) {
            $query->setParameter('instancia', $id_instancia);
        }

        $registro = $query->getResult();

        return $registro;

    }

    public function obtenerNumPromocionesCampanyaByFiltros($id_categoria, $id_instancia)
    {
        $dql = "
            SELECT np
              FROM RMProductoBundle:NumPromociones np
              JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH (np.idInstancia = ic.idInstancia)
              JOIN RMCategoriaBundle:Categoria cat WITH (np.idCategoria = cat.idCategoria)
              JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo)
             WHERE np.estado > -1
               AND ic.estado > -1
               AND gs.estado > -1
               AND cat.asociado = 1
               AND ic.idInstancia IN (:instancia)
        ";

        if ($id_categoria != null && $id_categoria > 0) {
            $dql .= " AND c.idCategoria IN (:categoria) ";
        }

        $dql .= " ORDER BY gs.nombre";


        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('instancia', $id_instancia);

        if ($id_categoria != null && $id_categoria > 0) {
            $query->setParameter('categoria', $id_categoria);
        }

        $registro = $query->getResult();

        return $registro;

    }

    /**
     * @param int $id_instancia
     *
     * @return array|null
     */
    public function findNumPromocionesByInstancia($id_instancia = 0)
    {
        if (!$id_instancia) {
            return null;
        }

        return
            $this->createQueryBuilder('n')
                ->join('n.idGrupo', 'g')
                ->where('n.idInstancia = :idInstancia AND n.estado > -1')
                ->andWhere('g.estado > -1 and g.tipo = :promocion')
                ->setParameter('idInstancia', $id_instancia)
                ->setParameter('promocion', GrupoSlots::PROMOCION)
                ->addOrderBy('n.idNumPro')
                ->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    /**
     * @param int $id_instancia
     * @param int $id_categoria
     *
     * @return array|null
     */
    public function findNumPromocionesByInstanciayCategoria($id_instancia = 0, $id_categoria = 0)
    {
        if (!$id_instancia || !$id_categoria) {
            return null;
        }

        return
            $this->createQueryBuilder('n')
                ->join('n.idGrupo', 'g')
                ->where('n.idInstancia = :idInstancia AND n.estado > -1')
                ->andWhere('n.idCategoria = :idCategoria')
                ->andWhere('g.estado > -1 and g.tipo = :promocion')
                ->addOrderBy('n.idNumPro')
                ->setParameter('idInstancia', $id_instancia)
                ->setParameter('idCategoria', $id_categoria)
                ->setParameter('promocion', GrupoSlots::PROMOCION)
                ->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    /**
     * @param int $id_instancia
     *
     * @return array|null
     */
    public function findNumPromocionesCreatividadByInstancia($id_instancia = 0)
    {
        if (!$id_instancia) {
            return null;
        }

        return $this->createQueryBuilder('n')
            ->join('n.idGrupo', 'g')
            ->leftJoin('n.promociones', 'p')
            ->where('n.idInstancia = :idInstancia AND n.estado > -1')
            ->andWhere('g.estado > -1 and g.tipo = :creatividad')
            ->addOrderBy('n.idNumPro')
            ->setParameter('idInstancia', $id_instancia)
            ->setParameter('creatividad', GrupoSlots::CREATIVIDADES)
            ->getQuery()->getResult(Query::HYDRATE_OBJECT);
    }

    /**
     * @param $id_instancia
     *
     * @return array
     */
    public function findTotalGenericasPorGrupoByInstancia($id_instancia)
    {
        if (!$id_instancia) {
            return [];
        }

        $dql = "
            SELECT DISTINCT gs.idGrupo, SUM(np.numGenericas) AS totalGenericas, gs.numSlots AS totalSlots
            FROM RMProductoBundle:NumPromociones np
            JOIN np.idGrupo gs WITH (np.idGrupo = gs.idGrupo)
            WHERE np.idInstancia = :idInstancia
            AND np.estado > -1
            AND gs.estado > -1
            GROUP BY np.idGrupo
        ";

        return $this->_em->createQuery($dql)
            ->setParameter('idInstancia', $id_instancia)
            ->getResult(Query::HYDRATE_ARRAY);
    }
}
