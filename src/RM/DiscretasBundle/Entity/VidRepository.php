<?php

namespace RM\DiscretasBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class VidRepository extends EntityRepository
{
    public function obtenerVariablesDiscretas($nombre = '', $tipoVar = 0)
    {

        $dql = "select d
			from RMDiscretasBundle:Vid d
			where d.estado = 1";

        if ($nombre !== '') {
            $dql .= " AND d.nombre LIKE :nombre";
        }
        if ($tipoVar > 0) {
            $dql .= " AND d.tipo = :tipo";
        }
        $dql .= " ORDER BY d.nombre";


        $query = $this->_em->createQuery($dql);
        if ($nombre !== '') {
            $query->setParameter('nombre', '%' . $nombre . '%');
        }
        if ($tipoVar > 0) {
            $query->setParameter('tipo', $tipoVar);
        }

        $registros = $query->getResult();

        return $registros;

    }

    public function findById($id)
    {
        return $this->createQueryBuilder('v')
            ->join('v.tipo', 'tipo')
            ->where('v.idVid = :id')
            ->andWhere('v.estado > -1')
            ->getQuery()
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    public function obtenerVDbyId($id_vid)
    {


        $dql = "select d
			from RMDiscretasBundle:Vid d
			where d.estado = 1
			AND d.idVid = :idvid";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idvid', $id_vid);

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerUnicoGrupoSegmentoByVid($id_vid)
    {

        $dql = "
			SELECT gs
            FROM RMDiscretasBundle:VidGrupoSegmento gs
            WHERE gs.estado > -1
			AND gs.idVid = :id_vid
			";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_vid', $id_vid);
        $query->setMaxResults(1);

        $registros = $query->getOneOrNullResult();

        return $registros;

    }

    public function obtenerGrupoSegmentoByVidGrupoSegmento($id_vid_grupo_segmento)
    {


        $dql = "
				SELECT gs
            FROM RMDiscretasBundle:VidGrupoSegmento gs
            WHERE gs.estado = 1
			AND gs.idVidGrupoSegmento = :id_vid_grupo_segmento";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_vid_grupo_segmento', $id_vid_grupo_segmento);
        $query->setMaxResults(1);

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerSegmentosByIdGrupo($id_vid_grupo_segmento)
    {


        $dql = "SELECT s
            FROM RMDiscretasBundle:VidSegmento s
            WHERE s.estado > -1
			      AND s.idVidGrupoSegmento = :id_grupo";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_grupo', $id_vid_grupo_segmento);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerSegmentosByIdGrupoAndGlobal($id_vid_grupo_segmento, $id_vid_segmento_global)
    {


        $dql = "
				SELECT s
            FROM RMDiscretasBundle:VidSegmento s
            WHERE s.estado = 1
			AND s.idVidGrupoSegmento IN (:vid_grupo_segmento)";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('vid_grupo_segmento', $id_vid_grupo_segmento)
        ;

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerSegmentoByIdSegmento($id_vid_segmento)
    {

        $dql = "
				SELECT s
            FROM RMDiscretasBundle:VidSegmento s
            WHERE s.estado = 1
			AND s.idVidSegmento = :id_segmento";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_segmento', $id_vid_segmento);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerSegmentosParametros($id_vid_segmento_global = -1, $modificado_global = -1)
    {

        $dql = "
				SELECT s
            FROM RMDiscretasBundle:VidSegmento s
            WHERE s.estado = 1";

        $query = $this->_em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerGSByCatAndVar($id_vid, $id_categoria)
    {


        $dql = "
				SELECT gs
	            FROM RMDiscretasBundle:VidGrupoSegmento gs
	            WHERE gs.estado = 1
				AND gs.idVid = :id_vid
				AND gs.idCategoria = :id_categoria";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_vid', $id_vid);
        $query->setParameter('id_categoria', $id_categoria);

        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerGSByMarcaAndVar($id_vid, $id_marca)
    {


        $dql = "
				SELECT gs
	            FROM RMDiscretasBundle:VidGrupoSegmento gs
	            WHERE gs.estado = 1
				AND gs.idVid = :id_vid
				AND gs.idMarca = :id_marca";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_vid', $id_vid);
        $query->setParameter('id_marca', $id_marca);

        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerGSByIdVarSinClasificacion($id_vid)
    {


        $dql = "
				SELECT gs
	            FROM RMDiscretasBundle:VidGrupoSegmento gs
	            WHERE gs.estado = 1
				AND gs.idVid = :id_vid
				AND gs.idMarca IS NULL
				AND gs.idCategoria IS NULL";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_vid', $id_vid);

        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerSegmentosGlobales($id_vid_segmento_global = -1)
    {

        $qb = $this->_em->createQueryBuilder()
            ->select('sc')
            ->from('RMDiscretasBundle:VidSegmentoGlobal', 'sc')
            ->where('sc.estado > -1');

        if ($id_vid_segmento_global !== -1) {
           $qb->andWhere('sc.idVidSegmentoGlobal = :id_vid_segmento_global')
               ->setParameter('id_vid_segmento_global', $id_vid_segmento_global);
        }

         $registros = $qb->getQuery()->getResult();

        /**
        $dql = "
				SELECT s
            FROM RMDiscretasBundle:VidSegmentoGlobal s
            WHERE s.estado > -1";

        if ($id_vid_segmento_global != -1) {
            $dql .= " AND s.idVidSegmentoGlobal IN (" . $id_vid_segmento_global . ")";
        }

        $query = $this->_em->createQuery($dql);

        $registros = $query->getResult();
         * */

        return $registros;

    }

    public function obtenerCriteriosGlobales()
    {


        $dql = "
				SELECT c
            FROM RMDiscretasBundle:VidCriterioGlobal c";

        $query = $this->_em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;

    }

    public function eliminarSegmentosByIdSegmento($id_vid_segmento)
    {


        $dql = "
				UPDATE RMDiscretasBundle:VidSegmento s
            	SET s.estado = -1
				WHERE s.idVidSegmento IN (:id_vid_segmento)";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('id_vid_segmento', $id_vid_segmento)
        ;

        $registros = $query->getResult();

        return $registros;
    }

    public function eliminarSegmentoGlobalByIdSegmento($id_vid_segmento_global)
    {


        $dql = "
				UPDATE RMDiscretasBundle:VidSegmentoGlobal s
            	SET s.estado = -1
				WHERE s.idVidSegmentoGlobal IN (:id_vid_segmento_global)";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('id_vid_segmento_global', $id_vid_segmento_global)
        ;

        $registros = $query->getResult();

        return $registros;
    }

    public function eliminarSegmentosMedianteGlobales($id_vid_segmento_global = -1, $modificado_global = -1)
    {


        $dql = "SELECT s
            	FROM RMDiscretasBundle:VidSegmento s";

        $query = $this->_em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;
    }

    public function eliminarSegmentosDelGrupo(VidGrupoSegmento $vidGrupo)
    {
        $dql = "
            UPDATE RMDiscretasBundle:VidSegmento s
            SET s.estado = -1
            WHERE s.idVidGrupoSegmento = :id_grupo_segmento
        ";

        try {
            $query = $this->_em
                ->createQuery($dql);


            $query
                ->setParameter('id_grupo_segmento', $vidGrupo->getIdVidGrupoSegmento());


            $query->execute();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function findVariablesDiscretasByTipo(Tipo $tipo)
    {
        $dql = "
            SELECT d
            FROM RMDiscretasBundle:Vid d
            WHERE d.estado > -1
            AND d.tipo = :tipo
        ";

        return $this->_em->createQuery($dql)
            ->setParameter('tipo', $tipo->getId())
            ->getResult();
    }

}