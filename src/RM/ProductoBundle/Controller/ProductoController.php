<?php

namespace RM\ProductoBundle\Controller;

use IMAG\LdapBundle\User\LdapUser;
use RM\AppBundle\Controller\RMController;
use RM\ProductoBundle\Form\Type\ProductType;
use Symfony\Component\HttpFoundation\Request;

class ProductoController extends RMController
{

    public function uploadImageAction(Request $request, $cod_sku)
    {

        $em = $this->get('rm.manager')->getManager();
        /** @var LdapUser $usuario */
        $usuario    = $this->get('security.token_storage')->getToken()->getUser();
        $cliente = $usuario->getCliente();

        $producto = $em->getRepository('RMProductoBundle:Producto')->findById($cod_sku);


        $form = $this->createForm(new ProductType(), $producto, [
            'action' => $this->generateUrl('direct_config_ficha_productos', ['cod_sku' => $cod_sku]),
            'method' => 'post'
        ]);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $producto->uploadImagen($cliente);
            $em->flush();
        }

        return $this->render('RMProductoBundle:Producto:index.html.twig', [
            'form' => $form->createView(),
            'producto' => $producto
        ]);

    }

}
