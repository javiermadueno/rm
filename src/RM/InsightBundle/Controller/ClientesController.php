<?php

namespace RM\InsightBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClientesController extends Controller
{

    /**
     * @Route(
     *      name="rm_insight.clientes.index",
     *      path="/index",
     *      methods={GET}
     * )
     */
    public function indexAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();

        $meses = $dm->getRepository('RMMongoBundle:ResultadoMensual')->findMesesDisponibles();

        $mes1 = array_pop($meses);
        $mes2 = array_pop($meses);

        $resultado1 = $dm->getRepository('RMMongoBundle:ResultadoMensual')->find($mes1['id']);
        $resultado2 = $dm->getRepository('RMMongoBundle:ResultadoMensual')->find($mes2['id']);





    }
}
