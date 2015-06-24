<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MarcaRepository extends EntityRepository
{
    public function obtenerMarcas()
    {


        $dql = "select m
		from RMProductoBundle:Marca m";

        $query = $this->_em->createQuery($dql);

        $registros = $query->getResult();

        return $registros;

    }

    public function obtenerMarca($id_marca)
    {


        $dql = "select m
		from RMProductoBundle:Marca m
		WHERE m.idMarca = :id_marca";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_marca', $id_marca);
        $registro = $query->getResult();

        return $registro;

    }

    public function obtenerMarcasByCategoria($id_categoria)
    {


        $dql = "select m
		from RMProductoBundle:Marca m, RMProductoBundle:Producto p
		WHERE p.activo > -1
		AND p.idMarca = m.idMarca
		AND p.idCategoria = :id_categoria
		OR p.idCategoria2 = :id_categoria
        OR p.idCategoria3 = :id_categoria
        OR p.idCategoria4 = :id_categoria
        OR p.idCategoria5 = :id_categoria
        OR p.idCategoria6 = :id_categoria
        OR p.idCategoria7 = :id_categoria
        OR p.idCategoria8 = :id_categoria
        OR p.idCategoria9 = :id_categoria
        OR p.idCategoria10 = :id_categoria
        OR p.idCategoria11 = :id_categoria
		GROUP BY m.nombre
		ORDER BY m.nombre ASC";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_categoria', $id_categoria);
        $registro = $query->getResult();

        return $registro;
    }

    public function obtenerMarcasByCategoriaYNivel($id_categoria, $nivel)
    {


        $dql = "select m
		from RMProductoBundle:Marca m, RMProductoBundle:Producto p
		WHERE p.activo > -1
		AND p.idMarca = m.idMarca
		AND p.idCategoria{$nivel} = :id_categoria
		GROUP BY m.nombre
		ORDER BY m.nombre ASC";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('id_categoria', $id_categoria);
        $registro = $query->getResult();

        return $registro;

    }

    public function obtenerMarcasByIdsCategoria($idsCategoria = [])
    {


        $dql = "SELECT DISTINCT m
            FROM RMProductoBundle:Marca m
            JOIN RMProductoBundle:Producto p WITH (p.idMarca = m.idMarca )
            WHERE p.idCategoria IN (:categoria)
            OR p.idCategoria2 IN (:categoria)
            OR p.idCategoria3 IN (:categoria)
            OR p.idCategoria4 IN (:categoria)
            OR p.idCategoria5 IN (:categoria)
            OR p.idCategoria6 IN (:categoria)
            OR p.idCategoria7 IN (:categoria)
            OR p.idCategoria8 IN (:categoria)
            OR p.idCategoria9 IN (:categoria)
            OR p.idCategoria10 IN (:categoria)
            OR p.idCategoria11 IN (:categoria)
            ";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('categoria', implode(',', array_map(function ($o) {
                        return $o->getIdCategoria();
                    }, $idsCategoria)));

        return $query->getResult();

    }
}
