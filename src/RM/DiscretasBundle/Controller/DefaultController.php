<?php

namespace RM\DiscretasBundle\Controller;

use RM\DiscretasBundle\Entity\Configuracion;
use RM\DiscretasBundle\Entity\ParametroConfiguracion;
use RM\DiscretasBundle\Entity\Vid;
use RM\DiscretasBundle\Form\Data\DiscretaBuscadorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function obtenerRegistrosAction($idOpcionMenuSup, $idOpcionMenuIzq, $tipoVar)
    {
        $servicio = $this->get("variablesDiscretas");

        $peticion         = $this->get('request');
        $variableDiscreta = new Vid ();
        $formulario       = $this->createForm(new DiscretaBuscadorType (), $variableDiscreta);

        $formulario->handleRequest($peticion);

        if ($formulario->isValid()) {

            $selectVar = $servicio->getDiscretas($variableDiscreta->getNombre(), $tipoVar);
        } else {
            $selectVar = $servicio->getDiscretas('', $tipoVar);
        }

        return $this->render('RMDiscretasBundle:Default:index.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'variables'       => $selectVar,
            'formulario'      => $formulario->createView()
        ]);
    }

    public function showConfiguracionAction($idOpcionMenuSup, $idOpcionMenuIzq)
    {
        $em             = $this->get('rm.manager')->getManager();
        $servicio       = $this->get("configuracion");


        $nivelAmplitud = $em
            ->getRepository('RMDiscretasBundle:Configuracion')
            ->findNivelGamaASegmentar();

        $parametros = $em
            ->getRepository('RMDiscretasBundle:ParametroConfiguracion')
            ->findParametrosConfiguracionByNivelAmplitud($nivelAmplitud);

        $result = $servicio->getConfigurationParameters();

        usort($parametros, function (ParametroConfiguracion $a, ParametroConfiguracion $b) {
            return strcmp($a->getNombre(), $b->getNombre());
        });


        return $this->render('RMDiscretasBundle:Default:configuracion.html.twig', [
            'idOpcionMenuSup'         => $idOpcionMenuSup,
            'idOpcionMenuIzq'         => $idOpcionMenuIzq,
            'parametros'              => $parametros,
            'configuraciones'         => $result
        ]);
    }

    public function insertConfigurationDataAction(Request $request)
    {
        $servicio       = $this->get("configuracion");

        $configuracion = $request->get('configuracion', []);
        $parametros    = $request->get('parametro', []);

        if (empty($configuracion) || empty($parametros)) {
            $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.error.actualizar');
            return $this->redirect($this->generateUrl('data_basic_configuracion'));
        }

        $servicio->guardaParametrosConfiguracion($parametros);
        $servicio->saveConfigurationParameters($configuracion);

        $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.ok.editar');

        return $this->redirect($this->generateUrl('data_basic_configuracion'));
    }
}
