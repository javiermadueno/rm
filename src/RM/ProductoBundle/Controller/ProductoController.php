<?php

namespace RM\ProductoBundle\Controller;

use League\Csv\Writer;
use RM\AppBundle\Controller\RMController;
use RM\ProductoBundle\Entity\CsvImagenes;
use RM\ProductoBundle\Form\Type\CsvImagenesType;
use RM\ProductoBundle\Form\Type\ProductType;
use RM\RMMongoBundle\Util;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ProductoController extends RMController
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

        $nombre = $nombre == -1 ? '' : urldecode($nombre);
        $codigo = $codigo == -1 ? '' : $codigo;


        $repo = $this->get('rm.manager')->getManager()
                     ->getRepository('RMProductoBundle:Producto')
        ;

        $numero_items = 5;
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
            $paginator->setItemsPerPage(5); // Para poner el numero de item que se quieren por pagina

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
     * @param Request $request
     * @param         $cod_sku
     *
     * @return Response|static
     * @throws \Exception
     */
    public function uploadImageAction(Request $request, $cod_sku)
    {

        $em = $this->get('rm.manager')->getManager();

        $producto = $em->getRepository('RMProductoBundle:Producto')->findById($cod_sku);

        $path_generator = $this
            ->get('rm.clientpathurlgenerator');

        $form = $this->createForm(new ProductType(), $producto, [
            'action' => $this->generateUrl('direct_config_ficha_productos', ['cod_sku' => $cod_sku]),
            'method' => Request::METHOD_POST
        ])
        ;

        $form->handleRequest($request);
        if ($form->isValid()) {

            $path = $path_generator
                ->getRutaImagenesProducto($absolute = true);

            $producto->uploadImagen($path);
            $em->flush();

            $imagen_url = $path_generator
                ->getRutaImagenProducto($producto->getImagen(), $absolute = false);

            if($request->isXmlHttpRequest()) {
                $data = [
                    'id_producto' => $producto->getIdProducto(),
                    'imagen' => $this->container->get('twig.extension.assets')
                        ->getAssetUrl($imagen_url,null, false, rand(100,999))
                ];

                return JsonResponse::create($data, Response::HTTP_OK);
            }
        }

        return $this->render('RMProductoBundle:Producto:index.html.twig', [
            'form'     => $form->createView(),
            'producto' => $producto
        ])
            ;

    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function uploadCsvImagesAction(Request $request)
    {
        $em = $this->getManager();

        $csv_imagenes = new CsvImagenes();
        $form         = $this->createForm(new CsvImagenesType(), $csv_imagenes,
            [
                'method' => 'post',
                'action' => $this->generateUrl('rm_producto_bundle.producto.upload_imagenes_csv')
            ]
        )
        ;

        $form->add('submit', 'submit', ['label' => 'boton.subir']);

        $form->handleRequest($request);
        if ($form->isValid()) {

            $web_path               = $this->container->getParameter('web_path');
            $ruta_extraccion        = $this->tempdir($web_path . '/tmp');
            $ruta_imagenes_producto = $this->get('rm.clientpathurlgenerator')->getRutaImagenesProducto($absolute = true);

            /** @var CsvImagenes $csv */
            $csv       = $form->getData();
            $productos = $csv->processFile($ruta_extraccion);

            $productos = $csv->moveImagenesProductoTo($productos, $ruta_imagenes_producto);

            $em->getRepository('RMProductoBundle:Producto')
               ->actualizaImagenesProducto($productos)
            ;

            Util::rmdir_recursive($ruta_extraccion);

            $this->addFlash('mensaje', 'mensaje.ok.editar');

            return $this->redirectToRoute('direct_config_listado_productos');
        }

        return $this->render('@RMProducto/Producto/formulario_imagenes_csv.html.twig', [
            'form' => $form->createView()
        ])
            ;

    }

    /**
     * @param $dir
     *
     * @return string
     */
    private function tempdir ($dir)
    {
        $temp_dir = tempnam($dir, '');
        unlink($temp_dir);
        mkdir($temp_dir);

        return $temp_dir;
    }

    /**
     * @return BinaryFileResponse
     */
    public function exportProductosSinImagenAction()
    {
        $em = $this->getManager();
        $trans = $this->get('translator');

        $productos = $em
            ->getRepository('RMProductoBundle:Producto')
            ->findProductosSinImagen();

        $csv = new \SplFileObject(tempnam(sys_get_temp_dir(), 'CSV'), 'a+');

        $writer = Writer::createFromFileObject($csv, 'w');
        $writer->setDelimiter(';');
        $writer->insertOne([
            $trans->trans('fichero.csv.columna.id'),
            $trans->trans('fichero.csv.columna.nombre')
        ]);
        $writer->insertAll($productos);


        $response =  new BinaryFileResponse($csv);
        $response->headers->set('Content-Type', 'text/csv');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'productos.csv'
        );

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return BinaryFileResponse
     */
    public function descargarInstruccionesAction(Request $request)
    {
        $locale = $request->getLocale();
        $ruta   = $this->container->getParameter('ruta.instrucciones');

        $instrucciones = $ruta . sprintf('%s_%s', $locale, 'instrucciones.txt');

        $response =  new BinaryFileResponse($instrucciones);
        $response->headers->set('Content-Type', 'text/plain');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'instrucciones.txt'
        );

        return $response;
    }

}
