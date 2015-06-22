<?php

namespace RM\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RMAppBundle:Default:index.html.twig', ['name' => $name]);
    }
}
