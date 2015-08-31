<?php

namespace RM\CategoriaBundle\Entity;

use Doctrine;
use Doctrine\ORM\EntityRepository;

class CategoriaRepository extends EntityRepository
{
    public function obtenerCategoria ($id_categoria)
    {


        $dql = "select c
			from RMCategoriaBundle:Categoria c
			WHERE c.idCategoria = :id_categoria";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_categoria', $id_categoria);
        $registro = $query->getResult();

        //var_dump($registro);

        return $registro;
    }

    public function obtenerCategorias ($id_nivel = -1, $asociado = -1, $id_categoria = '')
    {

        $dql = "select c
			from RMCategoriaBundle:Categoria c
			where 1=1
			and  c.estado > -1 ";
        if ($id_nivel > 0) {
            $dql .= " AND c.idNivelCategoria = :nivel";
        }
        if ($asociado > -1) {
            $dql .= " AND c.asociado = :asociado";
        }
        if ($id_categoria !== '') {
            $dql .= " AND c.idCategoria IN (" . $id_categoria . ")";
        }
        $dql .= " ORDER BY c.nombre";

        $query = $this->_em->createQuery($dql);
        if ($id_nivel > 0) {
            $query->setParameter('nivel', $id_nivel);
        }
        if ($asociado > -1) {
            $query->setParameter('asociado', $asociado);
        }

        $variables = $query->getResult();

        return $variables;

    }

    public function obtenerCatAsoc ()
    {

        $dql = "select c
			from RMCategoriaBundle:Categoria c
			WHERE  c.estado > -1
			AND c.asociado = 1";

        $query = $this->_em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerCatByInstancia ($id_instancia)
    {


        $dql = "select c
			from RMCategoriaBundle:Categoria c
			JOIN RMProductoBundle:NumPromociones As np WITH (c.idCategoria = np.idCategoria AND np.estado > -1)
			JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH ( ic.idInstancia = np.idInstancia)
			JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo and gs.estado > -1 )
			WHERE ic.estado > -1
			AND   np.idInstancia = :id_instancia
			GROUP BY c.idCategoria
			ORDER BY c.nombre";

        $query = $this->_em
            ->createQuery($dql)
            ->setParameter('id_instancia', $id_instancia);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerCatPermitidasByInstancia ($id_instancia, $categorias =  [])
    {


        $dql = "select c
			from RMCategoriaBundle:Categoria c
			JOIN RMProductoBundle:NumPromociones As np WITH (c.idCategoria = np.idCategoria AND np.estado > -1)
			JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH ( ic.idInstancia = np.idInstancia)
			JOIN RMPlantillaBundle:GrupoSlots gs WITH (np.idGrupo = gs.idGrupo and gs.estado > -1 )
			WHERE ic.estado > -1
			AND   np.idInstancia = :id_instancia
			AND c IN (:categorias)
			GROUP BY c.idCategoria
			ORDER BY c.nombre";

        if(empty($categorias)) {
            return [];
        }

        $query = $this->_em->createQuery($dql);
        $query
            ->setParameter('categorias', $categorias)
            ->setParameter('id_instancia', $id_instancia)
        ;

        $registros = $query->getResult();

        return $registros;
    }


    public function obtenerNivelesCategoria ()
    {

        $dql = "select n
			from RMCategoriaBundle:NivelCategoria n";

        $query     = $this->_em->createQuery($dql);
        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerCategoriasDeCampanya ()
    {


        $dql = "select c
			from RMCategoriaBundle:Categoria c
			JOIN RMProductoBundle:NumPromociones As np WITH (c.idCategoria = np.idCategoria AND np.estado > -1)
			JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH (np.idInstancia = ic.idInstancia AND ic.estado > -1 AND ic.fase = 2)
			WHERE c.asociado = 1
			AND  c.estado > -1
			GROUP BY c.idCategoria
			ORDER BY c.nombre";

        $query     = $this->_em->createQuery($dql);
        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerCategoriasPermitidasDeCampanya($categorias = []){


        $dql = "select c
			from RMCategoriaBundle:Categoria c
			JOIN RMProductoBundle:NumPromociones As np WITH (c.idCategoria = np.idCategoria AND np.estado > -1)
			JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH (np.idInstancia = ic.idInstancia AND ic.estado > -1 AND ic.fase = 2)
			WHERE c.asociado = 1
			AND  c.estado > -1
			AND c IN (:categorias)
			GROUP BY c.idCategoria
			ORDER BY c.nombre";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('categorias', $categorias);
        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerCategoriasDeCampanyaByNombre ($nombreCategorias =  [])
    {


        $dql = "select c
					from RMCategoriaBundle:Categoria c
					JOIN RMProductoBundle:NumPromociones As np WITH (c.idCategoria = np.idCategoria AND np.estado > -1)
					JOIN RMComunicacionBundle:InstanciaComunicacion ic WITH (np.idInstancia = ic.idInstancia AND ic.estado > -1 AND ic.fase = 2)
					WHERE c.asociado = 1
					AND  c.estado > -1
					AND c.nombre IN (:nombres)
					ORDER BY c.nombre";

        $categorias = array_map('trim', $nombreCategorias);

        $query = $this->_em->createQuery($dql)
            ->setParameter('nombres', $categorias);

        $registros = $query->getResult();

        return $registros;
    }

    public function obtenerCategoriasPorNombre ($nombreCategorias =  [])
    {


        $dql = "select c
					from RMCategoriaBundle:Categoria c
					WHERE c.asociado = 1
					AND  c.estado > -1
					AND c.nombre IN (:nombres)
					ORDER BY c.nombre";

        $categorias = array_map('trim', $nombreCategorias);

        $query = $this->_em->createQuery($dql)
            ->setParameter('nombres', $categorias);

        $registros = $query->getResult();

        return $registros;
    }

    public function findCategoriasByNombreYNivel($categorias = [], $idNivel)
    {
        $dql = "
            SELECT cat
            FROM RMCategoriaBundle:Categoria cat
            WHERE cat.asociado = 1
            AND cat.estado > -1
            AND cat IN (:nombres)
            AND cat.idNivelCategoria = :id_nivel
            ORDER BY cat.nombre
        ";

        if(empty($categorias)) {
            return [];
        }

        $categorias = array_map('trim', $categorias);

        return $this->_em->createQuery($dql)
            ->setParameter('nombres',$categorias )
            ->setParameter('id_nivel', $idNivel)
            ->getResult();
    }

    public function findCategoriasDeSegmentos()
    {

        $dql = "
            SELECT DISTINCT c
            FROM RMSegmentoBundle:Segmento s
            JOIN RMCategoriaBundle:Categoria c WITH (s.idCategoria = c.idCategoria)
            wHERE s.c_fecha_ini <= :fecha
            AND   s.c_fecha_fin >= :fecha
            ORDER BY c.nombre
        ";

        $categorias = $this->_em->createQuery($dql)
            ->setParameter('fecha', new \DateTime())
            ->getResult();


        return $categorias;
    }

    public function updateAsociacionCategoriasByNivel($id_nivel, $asociado)
    {
        $this->createQueryBuilder('u')
            ->update()
            ->set('u.asociado', ':asociado')
            ->where('u.idNivelCategoria = :id_nivel')
            ->setParameter('asociado', $asociado)
            ->setParameter('id_nivel', $id_nivel)
            ->getQuery()
            ->execute();
    }

    /**
    public function findCategoriasRestantesByInstacia($id_instancia)
    {

        $qb3 = $this->_em->createQueryBuilder()
            ->select('conf.valor')
            ->from('RMDiscretasBundle:Configuracion', 'conf')
            ->where('conf.nombre = :nombre')
            ->setParameter('nombre', 'nivel_category_manager');

        $qb2 = $this->_em->createQueryBuilder();

        $qb2
            ->select('nivel.idNivelCategoria')
            ->from('RMCategoriaBundle:NivelCategoria', 'nivel')
            ->where($qb2->expr()->);

        $this->_em->createQueryBuilder()
            ->from('RMCategoriaBundle:Categoria', 'c')
            ->where('c.idCategoria NOT IN (SELECT num.idCategoria FROM RMProductoBundle:NumPromociones num WHERE num.idInstancia = :idInstancia AND num.idCategoria IS NOT NULL')
            ->andWhere('c.idNivelCategoria = (SELECT FROM RMDiscretaBundle:Configuracion conf WHERE  ')

    }
     */
}