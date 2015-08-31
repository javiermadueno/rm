<?php

namespace RM\ProductoBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\ProductoBundle\Entity\Producto;
use RM\ProductoBundle\Form\Type\CsvImagenesType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 *
 * @package RM\ProductoBundle\Controller
 */
class DefaultController extends RMController
{
    /**
     * @param int $id_categoria
     * @param int $id_marca
     * @param int $codigo
     * @param int $nombre
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function showProductosAction($id_categoria = -1, $id_marca = -1, $codigo = -1, $nombre = -1)
    {
        $servicioMarca = $this->get("MarcaService");
        $servicioCat   = $this->get("categoriaservice");


        $selectMarcas     = $servicioMarca->getMarcas();
        $selectCategorias = $servicioCat->getCatAsociadas();


        asort($selectMarcas);
        asort($selectCategorias);

        $nombre = $nombre === -1 ? '' : urldecode($nombre);
        $codigo = $codigo === -1 ? '' : $codigo;


        $repo = $this->get('rm.manager')->getManager()
                     ->getRepository('RMProductoBundle:Producto')
        ;

        $numero_items = 10;
        $paginator    = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage($numero_items);


        $selectProductos = $paginator
            ->paginate($repo->findProductosByFiltro($id_categoria, $id_marca, $nombre, $codigo))
            ->getResult()
        ;


        return $this->render('RMProductoBundle:Default:index.html.twig', [
            'objMarcas'     => $selectMarcas,
            'objCategorias' => $selectCategorias,
            'objProductos'  => $selectProductos,
            'id_categoria'  => $id_categoria,
            'id_marca'      => $id_marca,
            'codigo'        => $codigo,
            'nombre'        => $nombre

        ])
            ;
    }

    /**
     * @param Request $request
     */
    public function actualizarProductosAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $paginator = $this->get('ideup.simple_paginator');
            $paginator->setItemsPerPage(10); // Para poner el numero de item que se quieren por pagina

            $nombre = $request->get('nombre');
            $nombre = str_replace('-', ' ', $nombre);

            $repo            = $this->getManager()->getRepository('RMProductoBundle:Producto');
            $selectProductos = $paginator->paginate(
                $repo->findProductosByFiltro(
                    $request->get('id_categoria'),
                    $request->get('id_marca'),
                    $nombre,
                    $request->get('codigo')))->getResult()
            ;


            return $this->render('RMProductoBundle:Default:listadoProductos.html.twig', [
                'objProductos' => $selectProductos,
                'id_categoria' => $request->get('id_categoria'),
                'id_marca'     => $request->get('id_marca'),
                'codigo'       => $request->get('codigo'),
                'nombre'       => $nombre
            ])
                ;
        } else {
            throw $this->createNotFoundException('Se ha producido un error de envio de la información');
        }
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function getUniqueVoucherAction()
    {
        $voucher = $this
            ->get('promocion.voucher.generator')
            ->generateUniqueVoucher()
        ;

        return JsonResponse::create([
            'voucher' => $voucher
        ])
            ;
    }


}
