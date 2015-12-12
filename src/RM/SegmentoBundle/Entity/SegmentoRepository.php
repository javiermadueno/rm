<?php

namespace RM\SegmentoBundle\Entity;

use Doctrine\ORM\EntityRepository;
use RM\DiscretasBundle\Entity\Tipo;
use RM\DiscretasBundle\Entity\Vid;
use RM\DiscretasBundle\Entity\VidGrupoSegmento;
use RM\DiscretasBundle\Entity\VidSegmento;

class SegmentoRepository extends EntityRepository
{
    /**
     * @param $id_segmento
     *
     * @return mixed
     */
    public function obtenerSegmentoById($id_segmento)
    {
        $dql = "
            select s
            from RMSegmentoBundle:Segmento s
            WHERE s.idSegmento IN (:id_segmento)
        ";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('id_segmento', $id_segmento);

        $registros = $query->getResult();

        return $registros;

    }

    /**
     * @param $id_comunicacion
     *
     * @return mixed
     */
    public function obtenerSegmentoByIdComunicacion($id_comunicacion)
    {

        $dql = "
            select s
            from RMSegmentoBundle:Segmento s
            JOIN RMComunicacionBundle:SegmentoComunicacion As sc WITH (s.idSegmento = sc.idSegmento)
            WHERE s.estado > -1
            AND sc.estado > -1
        ";

        if ($id_comunicacion != -1) {
            $dql .= " AND sc.idComunicacion IN ( :comunicacion )";
        }

        $dql .= " ORDER BY s.nombre";

        $query = $this->_em
            ->createQuery($dql);

        if ($id_comunicacion != -1) {
            $query->setParameter('comunicacion', $id_comunicacion);
        }

        $registros = $query->getResult();

        return $registros;
    }

    /**
     * @param $idSegmento
     *
     * @return mixed
     */
    public function obtenerSegmentosByInstancia($idSegmento)
    {

        $dql = "select s
		from RMSegmentoBundle:Segmento s
		JOIN RMComunicacionBundle:SegmentoComunicacion sc WITH (s.idSegmento = sc.idSegmento)
		JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH (ic.idSegmentoComunicacion = sc.idSegmentoComunicacion)
		WHERE s.idSegmento = (:segmento)
		GROUP BY s.idSegmento";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('segmento', $idSegmento);

        $registros = $query->getResult();

        return $registros;
    }

    /**
     * @param $id_vt
     *
     * @return Segmento[]
     */
    public function obtenerSegmentosByIdVt($id_vt)
    {
        return $qb = $this
            ->createQueryBuilder('s')
            ->where('s.estado = 1')
            ->andWhere('s.idVt = :idvt')
            ->setParameter('idvt', $id_vt)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param      $tipo
     * @param      $id_categoria
     * @param      $id_marca
     * @param null $fecha
     *
     * @return Segmento[]
     */
    public function obtenerSegmentosFiltrados($tipo, $id_categoria, $id_marca, $fecha = null)
    {
        $fecha = $fecha instanceof \DateTime ? $fecha : new \DateTime($fecha);

        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.idCategoria', 'categoria', 'with', 'categoria.asociado = 1')
            ->leftJoin('s.idMarca', 'marca')
            ->innerJoin('s.tipo', 'tipo')
            ->where('s.estado > -1')
            ->andWhere('s.c_fecha_ini <= :fecha')
            ->andWhere('s.c_fecha_fin > :fecha')
            ->setParameter('fecha', $fecha)
        ;

        if ($id_categoria > -1) {
            $qb->andWhere('categoria.idCategoria = :categoria')
                ->setParameter('categoria', $id_categoria);
        }

        if ($id_marca > -1) {
            $qb->andWhere('marca.idMarca = :marca')
                ->setParameter('marca', $id_marca);
        }

        if ($tipo != -1) {
            $qb->andWhere('tipo.codigo = :tipo')
                ->setParameter('tipo', $tipo);
        }

        $registros = $qb->getQuery()->getResult();


        return $registros;
    }

    /**
     * @param      $id_vid
     * @param      $categoria
     * @param      $marca
     * @param      $proveedor
     * @param null $fecha
     *
     * @return array
     */
    public function findSegmentosCompraProductoByIdVariableCategoriaMarca(
        $id_vid,
        $categoria,
        $marca,
        $proveedor,
        $fecha = null
    ) {
        $fecha = $fecha instanceof \DateTime ? $fecha : new \Datetime($fecha);

        $dql = "
            SELECT s
            FROM RMSegmentoBundle:Segmento s
            INNER JOIN RMDiscretasBundle:Tipo t WITH (s.tipo = t.id)
            WHERE s.estado > -1
            AND t.codigo = :tipo
            AND s.c_fecha_ini <= :fecha
            AND s.c_fecha_fin > :fecha
        ";

        if ($id_vid != -1) {
            $dql .= " AND s.idVid = :idVid";
        }

        if ($categoria != -1) {
            $dql .= " AND s.idCategoria = :idCategoria";
        }

        if ($marca != -1) {
            $dql .= " AND s.idMarca = :idMarca";
        }

        if ($proveedor != -1) {
            $dql .= " AND s.idProveedor = :idProveedor";
        }

        $query = $this->_em->createQuery($dql);

        $query->setParameter('tipo', Tipo::COMPRA_PRODUCTO)
              ->setParameter('fecha', $fecha);

        if ($id_vid != -1) {
            $query->setParameter('idVid', $id_vid);
        }

        if ($categoria != -1) {
            $query->setParameter('idCategoria', $categoria);
        }

        if ($marca != -1) {
            $query->setParameter('idMarca', $marca);
        }

        if ($proveedor != -1) {
            $query->setParameter('idProveedor', $proveedor);
        }

        return $query->getResult();
    }

    /**
     * Devuelve los segmentos de tipo RFM. Si se pasa el idVariable busca tambien por el Id de la variable
     *
     * @param $id_vil
     *
     * @return array
     */
    public function findSegmentosRFM($id_vil, $fecha = null)
    {
        $fecha = $fecha instanceof \DateTime ? $fecha : new \Datetime($fecha);

        $dql = "
            SELECT s
            FROM RMSegmentoBundle:Segmento s
            INNER JOIN RMDiscretasBundle:Tipo t WITH (s.tipo = t.id)
            WHERE s.estado > -1
            AND t.codigo = :tipo
            AND s.c_fecha_ini <= :fecha
            AND s.c_fecha_fin > :fecha
        ";

        if ($id_vil != -1) {
            $dql .= " AND s.idVil = :idVil";
        }

        $query = $this->_em->createQuery($dql);

        if ($id_vil != -1) {
            $query->setParameter('idVil', $id_vil);
        }

        return $query
            ->setParameter('tipo', Tipo::RFM)
            ->setParameter('fecha', $fecha)
            ->getResult();
    }

    /**
     * @param $id_vid
     *
     * @return array
     */
    public function findSegmentosHabitosCompra($id_vid, $fecha = null)
    {
        $fecha = $fecha instanceof \DateTime ? $fecha : new \Datetime($fecha);

        $dql = "
            SELECT s
            FROM RMSegmentoBundle:Segmento s
            INNER JOIN RMDiscretasBundle:Tipo t WITH (s.tipo = t.id)
            WHERE s.estado > -1
            AND t.codigo = :tipo
            AND s.c_fecha_ini <= :fecha
            AND s.c_fecha_fin > :fecha
        ";

        if ($id_vid != -1) {
            $dql .= " AND s.idVid = :idVid";
        }

        $query = $this->_em->createQuery($dql);

        if ($id_vid != -1) {
            $query->setParameter('idVid', $id_vid);
        }

        return $query
            ->setParameter('tipo', Tipo::HABITOS_COMPRA)
            ->setParameter('fecha', $fecha)
            ->getResult();
    }

    /**
     * @param $id_variable
     *
     * @return array
     */
    public function findSegmentosSocioDemograficos($id_variable, $fecha = null)
    {
        $lineales  = $this->findSegmentosSociodemograficoLineal($id_variable, $fecha);
        $discretos = $this->findSegmentosSocioDemograficoDiscreto($id_variable, $fecha);
        $registros = array_merge($lineales, $discretos);

        return $registros = array_unique($registros);
    }

    /**
     * @param $id_variable
     *
     * @return array
     */
    public function findSegmentosSociodemograficoLineal($id_variable, $fecha = null)
    {
        $fecha = $fecha instanceof \DateTime ? $fecha : new \Datetime($fecha);
        $dql   = "
            SELECT s
            FROM RMSegmentoBundle:Segmento s
            INNER JOIN RMDiscretasBundle:Tipo t WITH (s.tipo = t.id)
            WHERE s.estado > -1
            AND t.codigo = :tipo
            AND s.c_fecha_ini <= :fecha
            AND s.c_fecha_fin > :fecha
        ";

        if ($id_variable != -1) {
            $dql .= " AND s.idVil = :idVil";
        }

        $query = $this->_em->createQuery($dql);

        if ($id_variable != -1) {
            $query->setParameter('idVil', $id_variable);
        }

        return $query
            ->setParameter('tipo', Tipo::SOCIODEMOGRAFICO)
            ->setParameter('fecha', $fecha)
            ->getResult();
    }

    /**
     * @param $id_variable
     *
     * @return array
     */
    public function findSegmentosSocioDemograficoDiscreto($id_variable, $fecha = null)
    {
        $fecha = $fecha instanceof \DateTime ? $fecha : new \Datetime($fecha);

        $dql = "
            SELECT s
            FROM RMSegmentoBundle:Segmento s
            INNER JOIN RMDiscretasBundle:Tipo t WITH (s.tipo = t.id)
            WHERE s.estado > -1
            AND t.codigo = :tipo
            AND s.c_fecha_ini <= :fecha
            AND s.c_fecha_fin > :fecha
        ";

        if ($id_variable != -1) {
            $dql .= " AND s.idVid = :idVid";
        }

        $query = $this->_em->createQuery($dql);

        if ($id_variable != -1) {
            $query->setParameter('idVid', $id_variable);
        }

        return $query
            ->setParameter('tipo', Tipo::SOCIODEMOGRAFICO)
            ->setParameter('fecha', $fecha)
            ->getResult();
    }

    public function findSegmentosCicloVida($id_variable, $fecha = null)
    {
        $fecha = $fecha instanceof \DateTime ? $fecha : new \Datetime($fecha);
        $dql   = "
            SELECT s
            FROM RMSegmentoBundle:Segmento s
            INNER JOIN RMDiscretasBundle:Tipo t WITH (s.tipo = t.id)
            WHERE s.estado > -1
            AND t.codigo = :tipo
            AND s.c_fecha_ini <= :fecha
            AND s.c_fecha_fin > :fecha

        ";

        if ($id_variable != -1) {
            $dql .= " AND s.idVt = :idVt";
        }

        $query = $this->_em->createQuery($dql);

        if ($id_variable != -1) {
            $query->setParameter('idVt', $id_variable);
        }

        return $query
            ->setParameter('tipo', Tipo::CICLO_VIDA)
            ->setParameter('fecha', $fecha)
            ->getResult();
    }


    /**
     * @param           $id_variable
     * @param \DateTime $fecha
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function findSegmentosByIdVidSegmentoYFecha($id_variable, \DateTime $fecha)
    {
        $vid = $this->_em->find('RMDiscretasBundle:Vid', $id_variable);

        if (!$vid instanceof Vid) {
            return [];
        }

        $repo  = $this->_em->getRepository('RMDiscretasBundle:Vid');
        $grupo = $repo->obtenerUnicoGrupoSegmentoByVid($vid->getIdVid());

        if (!$grupo instanceof VidGrupoSegmento) {
            return [];
        }

        $segmentos = $repo->obtenerSegmentosByIdGrupo($grupo->getIdVidGrupoSegmento());

        $idsSegmentos = array_map(
            function (VidSegmento $segmento) {
                return $segmento->getIdVidSegmento();
            }, $segmentos);

        return $this
            ->createQueryBuilder('s')
            ->where('s.idVidSegmento IN (:ids) ')
            ->andWhere('s.c_fecha_ini <= :fecha')
            ->andWhere('s.c_fecha_fin > :fecha')
            ->setParameter('ids', $idsSegmentos)
            ->setParameter('fecha', $fecha)
            ->getQuery()
            ->getResult();
    }

    public function findSegmentosFidelidad()
    {
        return $this->findSegmentosByNombre([
            'Fidelidad_Habitual',
            'Fidelidad_Compartido',
            'Fidelidad_Ocasional',
            'Fidelidad_Fidelizado',
            'Fidelidad_Exclusivo'
        ]);
    }

    /**
     * @param array $nombres
     *
     * @return array|mixed
     */
    public function findSegmentosByNombre(array $nombres)
    {
        if (empty($nombres)) {
            return [];
        }

        $nombres = array_map('trim', $nombres);

        $segmentos = $this
            ->createQueryBuilder('s')
            ->select('s.c_clave', 's.nombre')
            ->where('s.nombre IN (:nombres)')
            ->andWhere('s.c_fecha_ini <= :fecha')
            ->andWhere('s.c_fecha_fin > :fecha')
            ->orderBy('s.idSegmento')
            ->setParameter('nombres', $nombres)
            ->setParameter('fecha', new \DateTime())
            ->getQuery()->getArrayResult();

        $segmentos = array_reduce($segmentos, function ($result, $elem) {
            $result[$elem['nombre']] = $elem['c_clave'];

            return $result;
        });

        return $segmentos;
    }

    public function findSegmentosSexo()
    {
        return $this->findSegmentosByNombre([
            'Sexo_MUJER',
            'Sexo_HOMBRE'
        ]);
    }

    public function findSegmentosRiesgo()
    {
        return $this->findSegmentosByNombre([
            'Riesgo_Alto',
            'Riesgo_Bajo',
            'Riesgo_Medio'
        ]);
    }

    public function findSegmentosEdad()
    {
        return $this->findSegmentosByNombre([
            'Fecha de nacimiento_niños',
            'Fecha de nacimiento_jovenes',
            'Fecha de nacimiento_adultos',
            'Fecha de nacimiento_maduros',
            'Fecha de nacimiento_jubilados',
            'Fecha de nacimiento_adolescentes',
            'Fecha de nacimiento_jovenes adultos'
        ]);
    }

    public function findSegmentosValor()
    {
        return $this->findSegmentosByNombre([
            'Valor_Alto',
            'Valor_Medio',
            'Valor_Bajo',
        ]);
    }

    public function findSegmentosFranjaHoraria()
    {
        return $this->findSegmentosByNombre([
            'Franja horaria mañana N últimos meses_Sí',
            'Franja horaria mediodia N últimos meses_Sí',
            'Franja horaria tarde N últimos meses_Sí',
            'Franja horaria noche N últimos meses_Sí',
        ]);
    }

    public function findSegmentosPreferenciaDia()
    {
        return $this->findSegmentosByNombre([
            'Preferencia Fin de Semana N últimos meses_Sí',
            'Preferencia L-J N últimos meses_Sí',
            'Preferencia V N últimos meses_Sí',
        ]);
    }

    public function findSegmentosGama()
    {
        return $this->findSegmentosByNombre([
            'Gama Basic_Sí',
            'Gama Estandar_Sí',
            'Gama Premium_Sí'
        ]);
    }

    public function findSegmentosEstado()
    {
        return $this->findSegmentosByNombre([
            'Estado_Activo',
            'Estado_Inactivo',
            'Estado_Nuevo'
        ]);
    }
}
