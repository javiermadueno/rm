<?php

namespace RM\ProductoBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\ProductoBundle\Entity\Producto;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends RMController
{
    public function showProductosAction(
        $idOpcionMenuSup,
        $idOpcionMenuIzq,
        $opcionMenuTabConfig,
        $id_categoria = -1,
        $id_marca = -1,
        $codigo = -1,
        $nombre = -1
    ) {
        $servicioMarca = $this->get("MarcaService");
        $servicioCat = $this->get("categoriaservice");
        $servicioTI = $this->get("TamanyoImagenService");
        $servicioPR = $this->get("ProductoService");

        $selectMarcas = $servicioMarca->getMarcas();
        $selectCategorias = $servicioCat->getCatAsociadas();
        $selectTamanyos = $servicioTI->getTIByTipo(0);

        asort($selectMarcas);
        asort($selectCategorias);

        $nombre = $nombre == -1 ? '' : urldecode($nombre);
        $codigo = $codigo == -1 ? '' : $codigo;

        $extensionFormatoImages = [".jpeg", ".jpg", ".gif", ".tiff", ".bmp", ".png"];

        $repo = $this->get('rm.manager')->getManager()->getRepository('RMProductoBundle:Producto');

        $numero_items = 10;
        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage($numero_items);


        $selectProductos = $paginator
            ->paginate($repo->findProductosByFiltro($id_categoria, $id_marca, $nombre, $codigo))->getResult();


        return $this->render('RMProductoBundle:Default:index.html.twig', [
            'idOpcionMenuSup'        => $idOpcionMenuSup,
            'idOpcionMenuIzq'        => $idOpcionMenuIzq,
            'opcionMenuTabConfig'    => $opcionMenuTabConfig,
            'objMarcas'              => $selectMarcas,
            'objCategorias'          => $selectCategorias,
            'objTamanyos'            => $selectTamanyos,
            'objProductos'           => $selectProductos,
            'extensionFormatoImages' => $extensionFormatoImages,
            'id_categoria'           => $id_categoria,
            'id_marca'               => $id_marca,
            'codigo'                 => $codigo,
            'nombre'                 => $nombre

        ]);
    }

    public function actualizarProductosAction()
    {
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $request = $this->container->get('request');
            $servicioPR = $this->get("ProductoService");
            $servicioTI = $this->get("TamanyoImagenService");

            $selectTamanyos = $servicioTI->getTIByTipo(0);

            $extensionFormatoImages = [".jpeg", ".jpg", ".gif", ".tiff", ".bmp", ".png"];

            $paginator = $this->get('ideup.simple_paginator');
            $paginator->setItemsPerPage(10); // Para poner el numero de item que se quieren por pagina

            $nombre = $request->get('nombre');
            $nombre = str_replace('-', ' ', $nombre);

            $repo = $this->getManager()->getRepository('RMProductoBundle:Producto');
            $selectProductos = $paginator->paginate(
                $repo->findProductosByFiltro(
                    $request->get('id_categoria'),
                    $request->get('id_marca'),
                    $nombre,
                    $request->get('codigo')))->getResult();


            return $this->render('RMProductoBundle:Default:listadoProductos.html.twig', [
                'objTamanyos'            => $selectTamanyos,
                'extensionFormatoImages' => $extensionFormatoImages,
                'objProductos'           => $selectProductos,
                'id_categoria'           => $request->get('id_categoria'),
                'id_marca'               => $request->get('id_marca'),
                'codigo'                 => $request->get('codigo'),
                'nombre'                 => $nombre
            ]);
        } else {
            throw $this->createNotFoundException('Se ha producido un error de envio de la informaci�n');
        }
    }


    public function getUniqueVoucherAction()
    {
        $voucher = $this->get('promocion.voucher.generator')->generateUniqueVoucher();

        return JsonResponse::create([
            'voucher' => $voucher
        ]);
    }





}
