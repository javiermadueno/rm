<?php

namespace RM\ProductoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductoController extends Controller
{
    public function indexAction()
    {
        return $this->render('RMProductoBundle:Producto:index.html.twig', array(
                // ...
            ));    }

}
