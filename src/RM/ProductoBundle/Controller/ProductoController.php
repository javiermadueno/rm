<?php

namespace RM\ProductoBundle\Controller;

use IMAG\LdapBundle\User\LdapUser;
use RM\AppBundle\Controller\RMController;
use RM\ProductoBundle\Entity\CsvImagenes;
use RM\ProductoBundle\Form\Type\CsvImagenesType;
use RM\ProductoBundle\Form\Type\ProductType;
use RM\RMMongoBundle\Util;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductoController extends RMController
{

    public function uploadImageAction(Request $request, $cod_sku)
    {

        $em = $this->get('rm.manager')->getManager();
        /** @var LdapUser $usuario */
        $usuario = $this->get('security.token_storage')->getToken()->getUser();
        $cliente = $usuario->getCliente();

        $producto = $em->getRepository('RMProductoBundle:Producto')->findById($cod_sku);


        $form = $this->createForm(new ProductType(), $producto, [
            'action' => $this->generateUrl('direct_config_ficha_productos', ['cod_sku' => $cod_sku]),
            'method' => 'post'
        ])
        ;

        $form->handleRequest($request);
        if ($form->isValid()) {
            $producto->uploadImagen($cliente);
            $em->flush();

            if($request->isXmlHttpRequest()) {
                $data = [
                    'id_producto' => $producto->getIdProducto(),
                    'imagen'      => $this->container->get('twig.extension.assets')
                        ->getAssetUrl("$cliente/imagenesProducto/" . $producto->getImagen(),null, false, rand(100,999))
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
            $ruta_extraccion        = $web_path . '/tmp/imagenes';
            $ruta_imagenes_producto = $web_path . '/' . $this->getUser()->getCliente() . '/imagenesProducto';

            /** @var CsvImagenes $csv */
            $csv       = $form->getData();
            $productos = $csv->processFile($ruta_extraccion);

            $productos = $csv->moveImagenesProductoTo($productos, $ruta_imagenes_producto);

            $em->getRepository('RMProductoBundle:Producto')
               ->actualizaImagenesProducto($productos)
            ;

            chown($ruta_extraccion, 666);
            Util::rmdir_recursive($ruta_extraccion);

            $this->addFlash('mensaje', 'mensaje.ok.editar');

            return $this->redirectToRoute('direct_config_listado_productos');
        }

        return $this->render('@RMProducto/Producto/formulario_imagenes_csv.html.twig', [
            'form' => $form->createView()
        ])
            ;

    }

}
