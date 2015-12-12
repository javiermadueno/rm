<?php

namespace RM\TransformadasBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\DiscretasBundle\Entity\Tipo;
use RM\TransformadasBundle\Entity\Vt;
use RM\TransformadasBundle\Form\Data\NuevaVarTransType;
use RM\TransformadasBundle\Form\Data\TransformadaBuscadorType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends RMController
{
    public function obtenerRegistrosAction($idOpcionMenuSup, $idOpcionMenuIzq, $tipoVar)
    {
    	$servicio = $this->get("variablesTransformadas");
    	 
    	//Creaci�n del formulario mediante clase
    	$peticion = $this->get('request');
    	$variableTransformada = new Vt();
    	$formulario = $this->createForm(new TransformadaBuscadorType(), $variableTransformada);
    	 
    	$formulario->handleRequest($peticion);
    	//*************************************
    	 
    	if ($formulario->isValid()) {
    		//Se ha hecho pulsado sobre el bot�n de buscar, es decir, tiene petici�
    		$selectVar = $servicio->getTransformadas($variableTransformada->getNombre(), $tipoVar);
    	}
    	else{
    		$selectVar = $servicio->getTransformadas('', $tipoVar);
    	}
    	 
    	return $this->render('RMTransformadasBundle:Default:index.html.twig', [
    			'idOpcionMenuSup' => $idOpcionMenuSup,
    			'idOpcionMenuIzq' => $idOpcionMenuIzq,
    			'variables' => $selectVar,
    			'formulario' => $formulario->createView(),
                'tipoVar'=> $tipoVar
    	]);
    	 
    }
    
    public function crearVarTransformadaAction(Request $request, $idOpcionMenuSup, $idOpcionMenuIzq, $tipoVar){
    	$servicio = $this->get("variablesTransformadas");
    	
    	//ECHO 'ENTRO EN CREARVARTRANSFORMADA';
    	//Creaci�n del formulario mediante clase
    	$peticion = $request;
    	$objVT = new Vt();
    	$formulario = $this->createForm(new NuevaVarTransType(), $objVT);
    	
    	$formulario->handleRequest($peticion);
    	//*************************************
    	
    	if ($formulario->isValid()) {
    		$objVT->setEstado(1);

            $tipoVar = $this->getManager()->getRepository('RMDiscretasBundle:Tipo')
                ->find($tipoVar);

            if($tipoVar) {
                $objVT->setTipo($tipoVar);
                $objTmp = $servicio->guardarObjeto($objVT);

                if ($objTmp) {
                    $this->get('session')->getFlashBag()->add('mensaje', 'crear_ok');
                } else {
                    $this->get('session')->getFlashBag()->add('mensaje', 'error_general');
                }

                if ($tipoVar->getId() == Vt::TIPO_OTRAS_TRANSFORMADAS) {
                    return $this->redirect($this->generateUrl('data_avanced_ot_editar', ['id_vt' => $objTmp->getIdVt()]));
                } elseif ($tipoVar->getId() == Vt::TIPO_CICLO_VIDA) {
                    return $this->redirect($this->generateUrl('data_avanced_cv_editar', ['id_vt'=> $objTmp->getIdVt()]));
                }
            }
    	}
    	else{
    		return $this->render('RMTransformadasBundle:Default:nuevaVar.html.twig', [
    				'idOpcionMenuSup' => $idOpcionMenuSup,
    				'idOpcionMenuIzq' => $idOpcionMenuIzq,
    				'formulario' => $formulario->createView()
    		]);
    	}
    }
}