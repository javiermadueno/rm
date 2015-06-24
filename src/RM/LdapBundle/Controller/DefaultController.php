<?php

namespace RM\LdapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('LdapBundle:Default:index.html.twig', array('name' => $name));
    }
}
