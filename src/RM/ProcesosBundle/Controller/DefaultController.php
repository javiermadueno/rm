<?php

namespace RM\ProcesosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ProcesosBundle:Default:index.html.twig', array('name' => $name));
    }
}
