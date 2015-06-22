<?php

namespace RM\ProductoBundle\Controller;

use RM\ProductoBundle\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
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
        $servicioCat   = $this->get("categoriaservice");
        $servicioTI    = $this->get("TamanyoImagenService");
        $servicioPR    = $this->get("ProductoService");

        $selectMarcas     = $servicioMarca->getMarcas();
        $selectCategorias = $servicioCat->getCatAsociadas();
        $selectTamanyos   = $servicioTI->getTIByTipo(0);

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
            $request    = $this->container->get('request');
            $servicioPR = $this->get("ProductoService");
            $servicioTI = $this->get("TamanyoImagenService");

            $selectTamanyos = $servicioTI->getTIByTipo(0);

            $extensionFormatoImages = [".jpeg", ".jpg", ".gif", ".tiff", ".bmp", ".png"];

            $paginator = $this->get('ideup.simple_paginator');
            $paginator->setItemsPerPage(10); // Para poner el numero de item que se quieren por pagina

            $nombre = $request->get('nombre');
            $nombre = str_replace('-', ' ', $nombre);

            $repo            = $this->get('doctrine')->getManager($_SESSION['connection'])->getRepository('RMProductoBundle:Producto');
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

    public function showFichaProductoAction($cod_sku)
    {

        $em = $this->get('doctrine')->getManager($_SESSION['connection']);

        $producto = $em->find('RMProductoBundle:Producto', $cod_sku);

        if (!$producto instanceof Producto) {
            return $this->createNotFoundException('mensaje.error.producto');
        }

        return $this->render('RMProductoBundle:Default:fichaProducto.html.twig', [
            'creation'        => "old",
            'objMarca'        => $producto->getIdMarca(),
            'selectedProduct' => $producto
        ]);
    }

    public function getUniqueVoucherAction()
    {
        $voucher = $this->get('promocion.voucher.generator')->generateUniqueVoucher();

        return JsonResponse::create([
            'voucher' => $voucher
        ]);
    }

    public function showFichaProductoCrearAction()
    {
        $servicioMarca = $this->get("MarcaService");

        $selectMarcas = $servicioMarca->getMarcas();

        $estado = 'new';

        return $this->render('RMProductoBundle:Default:fichaProducto.html.twig', [
            'creation'        => $estado,
            'objMarcas'       => $selectMarcas,
            'selectedProduct' => null
        ]);
    }

    public function uploadImageProductoAction()
    {

        $request          = $this->container->get('request');
        $id_producto      = $request->get('id_producto');
        $servicioProducto = $this->get("ProductoService");

        $usuario    = $this->get('security.context')->getToken()->getUser();
        $folderName = $usuario->getCliente();  //Identificacion del centro
        $myAssetUrl = $this->get('kernel')->getRootDir() . '/../web';

        //Recibe una imagen.
        if ($request->isMethod('POST')) {

            $exito = true;
            try {
                $carpetaCentro = $myAssetUrl . "/" . $folderName;
                if (!file_exists($carpetaCentro)) {
                    mkdir($carpetaCentro);
                }

                $carpetaPlantilla = $carpetaCentro . "/imagenesProducto";
                if (!file_exists($carpetaPlantilla)) {
                    mkdir($carpetaPlantilla);
                }

                $arrayExt  = explode(".", basename($_FILES ["uploadFile"] ["name"]));
                $extension = $arrayExt[1];

                $carpetaFicPlantilla = $carpetaPlantilla . "/" . $id_producto . "." . $extension;

                if (!move_uploaded_file($_FILES['uploadFile']['tmp_name'], $carpetaFicPlantilla)) {
                    $exito = false;
                }
                $this->get('ladybug')->log($carpetaFicPlantilla);

            } catch (Exception $e) {
                $exito = false;
            }

            if ($exito) {
                $this->get('session')->getFlashBag()->add('mensaje', 'editar_ok');
            } else {
                $this->get('session')->getFlashBag()->add('mensaje', 'error_general');
            }

            return $this->render('::logMensajes.html.twig');
        } else {
            throw $this->createNotFoundException('Se ha producido un error de envio de la informaciÃ³n');
        }
    }
}
