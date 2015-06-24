<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 19/06/2015
 * Time: 9:46
 */

namespace RM\SegmentoBundle\DependencyInjection;


use RM\AppBundle\DependencyInjection\DoctrineManager;
use RM\CategoriaBundle\DependencyInjection\CategoriaServicio;
use RM\ProductoBundle\DependencyInjection\MarcaServicio;
use Symfony\Component\HttpFoundation\Request;

class BuscadorSegmentos
{

    public function __construct(
        SegmentoServicio $segmentoServicio,
        MarcaServicio $marcaServicio,
        CategoriaServicio $categoriaServicio,
        DoctrineManager $manager
    ) {
        $this->repo_segmentos = $segmentoServicio;
        $this->repo_marcas = $marcaServicio;
        $this->repo_categorias = $categoriaServicio;
        $this->em = $manager->getManager();
    }

    public function findSegmentos(Request $request)
    {
        $id_categoria = $request->query->get('id_categoria', -1);
        $id_proveedor = $request->query->get('proveedor', -1);
        $id_variable = $request->query->get('variables', -1);
        $id_marca = $request->query->get('id_marca', -1);
        $tipo = $request->query->get('tipo', -1);

        $tipoVariable = $this->em->getRepository('RMDiscretasBundle:Tipo')
            ->findOneBy(['codigo' => $tipo]);


        $segmentos = $this->repo_segmentos
            ->getSegmentosFiltrados($tipoVariable, $id_categoria, $id_marca, $id_proveedor, $id_variable);

        return $segmentos;
    }
}