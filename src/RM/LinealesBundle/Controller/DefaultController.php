<?php

namespace RM\LinealesBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\DiscretasBundle\Entity\Tipo;
use RM\DiscretasBundle\Entity\Vid;
use RM\LinealesBundle\Entity\Vil;
use RM\LinealesBundle\Form\Data\LinealBuscadorType;

class DefaultController extends RMController
{
    public function indexAction($idOpcionMenuSup)
    {
        return $this->render('RMLinealesBundle:Default:index.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup
        ]);
    }

    public function obtenerRegistrosAction($idOpcionMenuSup, $idOpcionMenuIzq, $tipoVar)
    {
        $servicio = $this->get("variablesLineales");
        $varDiscretaService = $this->get('variablesDiscretas');


        //Creaci�n del formulario mediante clase
        $peticion = $this->getRequest();
        $variableLineal = new Vil();
        $formulario = $this->createForm(new LinealBuscadorType(), $variableLineal);

        $formulario->handleRequest($peticion);
        //*************************************

        if ($formulario->isValid()) {
            //Se ha hecho pulsado sobre el bot�n de buscar, es decir, tiene petici�
            $selectVar = $servicio->getLineales($variableLineal->getNombre(), $tipoVar);
            $pagina = $peticion->get('page');
        } else {
            $selectVar = $servicio->getLineales('', $tipoVar);
            $pagina = 1;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $selectVar,
            $pagina,
            $this->container->getParameter('num_registros_pagina')
        );

        return $this->render('RMLinealesBundle:Default:listado.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'variables'       => $pagination,
            'formulario'      => $formulario->createView()
        ]);

    }

    public function obtenerVariablesSociodemograficasAction($idOpcionMenuSup, $idOpcionMenuIzq, $tipoVar)
    {
        $servicio = $this->get('sociodemograficasService');

        $tipoSociodemografica = $tipoVar;

        $peticion = $this->get('request');
        $variableLineal = new Vil();
        $formulario = $this->createForm(new LinealBuscadorType(), $variableLineal);

        $formulario->handleRequest($peticion);

        if ($formulario->isValid()) {
            //Se ha hecho pulsado sobre el botón de buscar, es decir, tiene petición
            $resultado = $servicio->findVariablesSociodemograficasPorNombre($variableLineal->getNombre());
        } else {
            $resultado = $servicio->obtenerVariableSociodemograficas();
        }


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $resultado,
            $peticion->get('page', 1),
            $this->container->getParameter('num_registros_pagina')
        );

        return $this->render('RMLinealesBundle:Default:listadoVariableSociodemograficas.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'variables'       => $pagination,
            'formulario'      => $formulario->createView()
        ]);

    }

    public function muestraInformacionVariableAction($idVariable = 0, $idOpcionMenuIzq)
    {
        if (!$idVariable) {
            $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.error.variable');

            return $this->redirect(
                $this->generateUrl('data_avanced_sociodemografico',
                    [
                        'tipoVar' => Tipo::SOCIODEMOGRAFICO
                    ]
                ));
        }

        $em = $this->getManager();

        $lineal = $em->find('RMLinealesBundle:Vil', $idVariable);
        $discreta = $em->find('RMDiscretasBundle:Vid', $idVariable);

        $vil = $lineal instanceof Vil ? $lineal : ($discreta instanceof Vid ? $discreta : null);

        if (!$vil || !in_array($vil->getTipo()->getCodigo(), [Tipo::SOCIODEMOGRAFICO])) {
            return $this->redirect(
                $this->generateUrl('data_avanced_sociodemografico',
                    [
                        'tipoVar' => Tipo::SOCIODEMOGRAFICO
                    ]
                ));
        }

        $servicio = $this->get('sociodemograficasService');

        if ($vil instanceof Vil) {
            $datosVariable = $servicio->findDatosVariableSocioDemograficaLineal($vil);
        } elseif ($vil instanceof Vid) {
            $datosVariable = $servicio->findDatosVariableSociodemograficaDiscreta($vil);
        } else {
            $this->createNotFoundException('La id de la variable no es correcta');
        }


        return $this->render('@RMLineales/Default/informacionVariableLineas.html.twig',
            [
                'idOpcionMenuIzq' => $idOpcionMenuIzq,
                'vil'             => $vil,
                'datos'           => $datosVariable

            ]);
    }


}
