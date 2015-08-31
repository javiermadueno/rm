<?php

namespace RM\TransformadasBundle\Entity;


use Doctrine\ORM\EntityRepository;

class VtRepository extends EntityRepository
{
    public function obtenerVariablesTransformadas($nombre = '', $tipoVar = 0)
    {
        $dql = "SELECT t, a
			FROM RMTransformadasBundle:Vt t
			JOIN t.tipo a
			where t.estado > -1";
        if ($nombre !== '') {
            $dql .= " AND t.nombre LIKE :nombre";
        }
        if ($tipoVar > 0) {
            $dql .= " AND a.id = :tipo";
        }
        $dql .= " ORDER BY t.nombre";

        $query = $this->_em->createQuery($dql);
        if ($nombre !== '') {
            $query->setParameter('nombre', '%' . $nombre . '%');
        }
        if ($tipoVar > 0) {
            $query->setParameter('tipo', $tipoVar);
        }

        $variables = $query->getResult();

        return $variables;
    }

    public function obtenerVTbyId($id_vt)
    {
        $dql = "select t
			from RMTransformadasBundle:Vt t
			join t.tipo a
			where t.estado = 1
			AND t.idVt = :idvt";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idvt', $id_vt);

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerSegmentoVTbyId($id_vt_segmento)
    {


        $dql = "select s
			from RMTransformadasBundle:VtSegmento s
			where s.idVtSegmento IN ( :id_vt_segmento )";

        $query     = $this->_em
            ->createQuery($dql)
            ->setParameter('id_vt_segmento', $id_vt_segmento)
        ;
        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerSegmentosVTbyIdVt($id_vt)
    {

        $dql = "select s
			from RMTransformadasBundle:VtSegmento s
			where s.estado = 1
			AND s.idVt = :idVt
			ORDER BY s.orden";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idVt', $id_vt);

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerGruposVTbyIdVt($id_vt)
    {
        $grupos = $this->_em->createQueryBuilder()
            ->select('grupo')
            ->from('RMTransformadasBundle:VtGrupo', 'grupo')
            ->join('grupo.idVtSegmento', 'segmento')
            ->where('segmento.idVt = :vt')
            ->andWhere('segmento.estado > -1')
            ->andWhere('grupo.estado > -1')
            ->orderBy('grupo.orden')
            ->setParameter('vt', $id_vt)
            ->getQuery()
            ->getResult()
        ;

        return $grupos;
    }

    public function obtenerIntervalosVTbyIdVt($id_vt)
    {


        $dql = "select  i
				from 	RMTransformadasBundle:VtSegmento s
				JOIN	RMTransformadasBundle:VtGrupo g WITH s.idVtSegmento = g.idVtSegmento
				JOIN	RMTransformadasBundle:VtIntervalo i WITH g.idGrupo = i.idGrupo
				where 	s.estado = 1
				AND 	g.estado = 1
				AND 	i.estado = 1
				AND 	s.idVt = :idVt";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idVt', $id_vt);

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerGrupoVTbyId($id_grupo)
    {


        $dql = "select g
			from RMTransformadasBundle:VtGrupo g
			where g.idGrupo IN (:grupo)";

        $query     = $this->_em
            ->createQuery($dql)
            ->setParameter('grupo', $id_grupo)
        ;
        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerGruposVTbyIdSegmento($id_vt_segmento)
    {


        $dql = "select g
			from RMTransformadasBundle:VtGrupo g
			where g.estado = 1
			AND g.idVtSegmento IN ( :id_vt_segmento )";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('id_vt_segmento', $id_vt_segmento)
        ;

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerIntervaloVTbyId($id_intervalo)
    {


        $dql = "select i
				from RMTransformadasBundle:VtIntervalo i
				WHERE i.idIntervalo IN ( :intervalo )";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('intervalo', $id_intervalo)
        ;

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerIntervalosVTbyIdGrupo($id_grupo)
    {


        $dql = "select i
			from RMTransformadasBundle:VtIntervalo i
			where i.estado = 1
			AND i.idGrupo IN ( :grupo )";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('grupo', $id_grupo)
        ;

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerSegmentos($id_vt)
    {


        $dql = "select s
			from RMTransformadasBundle:VtSegmento s
			JOIN RMTransformadasBundle:VtGrupo g
			JOIN RMTransformadasBundle:VtIntervalo i
			where s.estado = 1
			AND   g.estado = 1
			AND	  i.estado = 1
			AND s.idVt = :idvt
			GROUP BY s.idVtSegmento";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('idvt', $id_vt);

        $registros = $query->getResult();

        return $registros;

    }

    public function findById($id)
    {
        return $this->createQueryBuilder('v')
            ->join('v.segmentos', 'segmentos')
            ->join('segmentos.grupos', 'grupos')
            ->join('grupos.intervalos', 'intervalos')
            ->join('intervalos.idVil', 'vil')
            ->where('v.idVt = :id')
            ->andwhere('v.estado > -1')
            ->andWhere('segmentos.estado >-1')
            ->andWhere('grupos.estado > -1')
            ->andWhere('intervalos.estado > -1')
            ->getQuery()
            ->setParameter('id', $id)
            ->getSingleResult();
    }

}