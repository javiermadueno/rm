<?php

namespace RM\ProductoBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductoRepository extends EntityRepository
{

    /**
     * @param $id
     *
     * @return Producto
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById($id)
    {
        $producto = $this->createQueryBuilder('producto')
            ->join('producto.idMarca', 'marca')
            ->addSelect('marca')
            ->where('producto.idProducto = :id')
            ->andWhere('producto.activo = 1')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $producto;
    }

    public function obtenerProductosByFiltroDQL($id_categoria, $id_marca, $codigo)
    {


        $dql = "SELECT p
		FROM RMProductoBundle:Producto p
		WHERE p.activo > -1";

        if ($id_marca > 0) {
            $dql .= " AND p.idMarca = (" . $id_marca . ")";
        }

        if ($id_categoria > 0) {
            $dql .= " AND p.idCategoria IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria2 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria3 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria4 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria5 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria6 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria7 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria8 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria9 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria10 IN (" . $id_categoria . ")";
            $dql .= " OR p.idCategoria11 IN (" . $id_categoria . ")";
        }

        if ($codigo != '') {
            $dql .= " AND p.codSku IN (" . $codigo . ")";
        }

        $dql .= " ORDER BY p.codSku ASC";


        $query    = $this->_em->createQuery($dql);
        $registro = $query->getResult();

        return $registro;
    }

    /**
     * @param $categoria
     * @param $marca
     *
     * @return array
     */
    public function findProductosByCategoriaYMarca($categoria, $marca)
    {
        $productos = $this->createQueryBuilder('p')
            ->where('p.activo = 1')
            ->andWhere('(p.idCategoria  = :categoria OR
                 p.idCategoria2  = :categoria OR
                 p.idCategoria3  = :categoria OR
                 p.idCategoria4  = :categoria OR
                 p.idCategoria5  = :categoria OR
                 p.idCategoria6  = :categoria OR
                 p.idCategoria7  = :categoria OR
                 p.idCategoria8  = :categoria OR
                 p.idCategoria9  = :categoria OR
                 p.idCategoria10 = :categoria OR
                 p.idCategoria11 = :categoria)
                 ')
            ->andWhere('p.idMarca = :marca')
            ->setParameter('categoria', $categoria)
            ->setParameter('marca', $marca)
            ->getQuery()
            ->getResult();

        return $productos;
    }

    public function obtenerProductosByMarca($id_marca)
    {

        $dql = "SELECT p
               FROM RMProductoBundle:Producto p
               WHERE p.idMarca = :id_marca
               AND p.activo > -1";

        $query = $this->_em->createQuery($dql);

        $query->setParameter('id_marca', $id_marca);

        return $query->getResult();
    }


    public function obtenerProductosByCodigo($codigo)
    {
        $dql = "SELECT p
               FROM RMProductoBundle:Producto p
               WHERE p.codSku = :codigo
               AND p.activo > -1";

        $query = $this->_em->createQuery($dql);

        $query->setParameter('codigo', $codigo);

        return $query->getResult();
    }

    public function findProductosByFiltro($id_categoria = 0, $id_marca = 0, $nombre = '', $codigo = '')
    {

        $qb = $this->createQueryBuilder('p')
            ->where('p.activo = 1');

        if ($codigo != '') {
            return $qb->andWhere('p.idProducto = :codigo')
                ->setParameter('codigo', $codigo)
                ->getQuery()->getResult();
        }

        if ($id_categoria > 0) {
            $qb->andWhere(
                '(p.idCategoria  = :categoria OR
                 p.idCategoria2  = :categoria OR
                 p.idCategoria3  = :categoria OR
                 p.idCategoria4  = :categoria OR
                 p.idCategoria5  = :categoria OR
                 p.idCategoria6  = :categoria OR
                 p.idCategoria7  = :categoria OR
                 p.idCategoria8  = :categoria OR
                 p.idCategoria9  = :categoria OR
                 p.idCategoria10 = :categoria OR
                 p.idCategoria11 = :categoria)
                 '
            )->setParameter('categoria', $id_categoria);
        }

        if ($id_marca > 0) {
            $qb->andWhere('p.idMarca = :marca')
                ->setParameter('marca', $id_marca);
        }

        if ($nombre != '') {
            $qb->andWhere($qb->expr()->like('p.nombre', ':nombre'))
                ->setParameter('nombre', sprintf('%%%s%%', $nombre));
        }

        $productos = $qb->orderBy('p.idProducto')
            ->getQuery()
            ->getResult()
        ;


        return $productos;


    }


}
