<?php

namespace RM\PlantillaBundle\Controller;

use RM\PlantillaBundle\Form\TamanyoImagen\nuevoTamanyoCreatividadType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use RM\PlantillaBundle\Form\TamanyoImagen\nuevoTamanyoMarcaType;
use RM\PlantillaBundle\Form\TamanyoImagen\nuevoTamanyoProductoType;
use RM\PlantillaBundle\Entity\TamanyoImagen;
use Symfony\Component\HttpFoundation\Request;

class TamanyoImagenController extends Controller
{
    public function indexAction($idOpcionMenuSup, $idOpcionMenuIzq, $opcionMenuTabConfig)
    {
    	$servicioTI = $this->get("TamanyoImagenService");
    	
    	$objTamImgPro = $servicioTI->getTIConInfoAsocByTipo(TamanyoImagen::PRODUCTO);
        $objTamImgCre = $servicioTI->getTIConInfoAsocByTipo(TamanyoImagen::CREATIVIDAD);
    	
    	return $this->render('RMPlantillaBundle:TamanyoImagen:index.html.twig', array(
					'idOpcionMenuSup' => $idOpcionMenuSup,
					'idOpcionMenuIzq' => $idOpcionMenuIzq,
					'opcionMenuTabConfig' => $opcionMenuTabConfig,
					'objTamImgPro' => $objTamImgPro,
                    'objTamImgCre' => $objTamImgCre,
                    'tamanyosImagen' => array(
                        TamanyoImagen::PRODUCTO => $objTamImgPro,
                        TamanyoImagen::CREATIVIDAD  => $objTamImgCre
                    )
			));
    }
    
    public function deleteTamanyoAction()
    {
    	if($this->container->get('request')->isXmlHttpRequest()){
    		$request = $this->container->get('request');
    		
    		$tipo = $request->get('tipo');
    		$elementosBorrar = $request->get('elementosBorrar'. $tipo);
    		
    		$servicioTI = $this->get("TamanyoImagenService");
    		
    		$respuesta = $servicioTI->eliminarTamanyosById($elementosBorrar);
    	
    		if($respuesta == 1){
    			$this->get('session')->getFlashBag()->add('mensaje','eliminar_ok');
    		}
    		else{
    			$this->get('session')->getFlashBag()->add('mensaje','error_general');
    		}
    		
    		$objTamImg = $servicioTI->getTIConInfoAsocByTipo($tipo);
    	
    		return $this->render('RMPlantillaBundle:TamanyoImagen:listadoTamanyo.html.twig', array(
    				'objTamImg' => $objTamImg,
    				'tipoImg' => $tipo
    		));
    	}
    	else{
    		throw $this->createNotFoundException('Se ha producido un error de envio de la información');
    	}
    }
    
    public function nuevoTamanyoAction($idOpcionMenuSup, $idOpcionMenuIzq, $opcionMenuTabConfig, $tipoEntidad)
    {
    	
    		$objTam = new TamanyoImagen;

    		if($tipoEntidad == "Producto"){
    			$formulario = $this->createForm(new nuevoTamanyoProductoType(), $objTam, array('selected' => TamanyoImagen::PRODUCTO));
    		}
            else{
                $formulario = $this->createForm(new nuevoTamanyoProductoType(), $objTam, array('selected' => TamanyoImagen::CREATIVIDAD));
            }


    		return $this->render('RMPlantillaBundle:TamanyoImagen:nuevoTamanyo.html.twig', array(
    				'idOpcionMenuSup' => $idOpcionMenuSup,
    				'idOpcionMenuIzq' => $idOpcionMenuIzq,
    				'opcionMenuTabConfig' => $opcionMenuTabConfig,
    				'formulario' => $formulario->createView()
    		));
    		
    	
    }
    
    public function guardarTamanyoAction(Request $request)
    {
    	if ($request->isMethod('POST')) {
    		
    		$objTam = new TamanyoImagen;

            $form = $this->createForm(new nuevoTamanyoProductoType(), $objTam, array(
                    'action'=> $this->generateUrl('direct_config_tamanyo_guardar'),
                ));

            $form->handleRequest($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager($_SESSION['connection']);
                $em->persist($objTam);
                $em->flush();
                $this->get('session')->getFlashBag()->add('mensaje','crear_ok');
            }

    		return $this->redirect($this->generateUrl('direct_config_tamanyo'));
    	}
    	else{
    		throw $this->createNotFoundException('Se ha producido un error de envio de la información');
    	} 
    }
}