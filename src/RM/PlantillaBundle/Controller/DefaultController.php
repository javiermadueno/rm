<?php

namespace RM\PlantillaBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Event\ComunicacionEvent;
use RM\ComunicacionBundle\Event\ComunicacionEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use RM\PlantillaBundle\Entity\Plantilla;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DefaultController extends RMController
{
    public function indexAction($idOpcionMenuSup, $idOpcionMenuIzq, $opcionMenuTabComunicacion, $id_comunicacion)
    {
        $em = $this->getManager();
        $servicioPlantilla = $this->get("PlantillaService");
        $servicioCom = $this->get("ComunicacionService");


        $objComunicaciones = $servicioCom->getComunicacionById($id_comunicacion);
        $objComunicacion = $objComunicaciones[0];

        $plantilla  = $objComunicacion->getPlantilla();

        if(!$plantilla instanceof Plantilla){

            $this->get('event_dispatcher')
                ->dispatch(ComunicacionEvents::NUEVA_COMUNICACION, new ComunicacionEvent($objComunicacion));
        }


        $selectGrupos = $servicioPlantilla->getGruposConNumeroSlots($plantilla->getIdPlantilla());

        return $this->render('RMPlantillaBundle:Default:index.html.twig', [
                'idOpcionMenuSup' => $idOpcionMenuSup,
                'idOpcionMenuIzq' => $idOpcionMenuIzq,
                'opcionMenuTabComunicacion' => $opcionMenuTabComunicacion,
                'id_comunicacion' => $id_comunicacion,
                'id_plantilla' => $plantilla->getIdPlantilla(),
                'objComunicacion' => $objComunicacion,
                'selectGrupos' => $selectGrupos
            ]);
    }

    public function importarPlantillaAction($id_canal, $id_comunicacion)
    {

        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($id_comunicacion);


        if($comunicacion->getIdCanal()->getIdCanal() != $id_canal){
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        }


        $plantillas = $em->getRepository('RMPlantillaBundle:Plantilla')->findBy([
                'esModelo' => true,
                'canal'  => $id_canal], [
                'idPlantilla' => 'DESC'
            ]);

        return $this->render('RMPlantillaBundle:Default:importarPlantilla.html.twig', [
                'id_comunicacion' => $id_comunicacion,
                'objComunicacion' => $comunicacion,
                'objPlantillas' => $plantillas
        ]);


    	
    }
    
    public function exportarPlantillaAction($id_canal, $id_comunicacion)
    {
    	$servicioCom = $this->get("ComunicacionService");
    
    	$objComunicaciones = $servicioCom->getComunicacionById($id_comunicacion);
    	 
    	if (!$objComunicaciones) {
    		throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
    	}
    	else{
    		$objComunicacion = $objComunicaciones[0];
    		if($objComunicacion->getIdCanal()->getIdCanal() != $id_canal){
    			throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
    		}
    		else{
    			return $this->render('RMPlantillaBundle:Default:exportarPlantilla.html.twig', [
    					'objComunicacion' => $objComunicacion
    			]);
    		}
    	}
    }
    
    //Leemos el fichero y devolvemos el contenido
    public function previsualizarPlantillaAction($id_comunicacion)
    {

        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($id_comunicacion);

        $plantilla = $comunicacion->getPlantilla();

        $fichero = $this->get('rm_plantilla_genera_plantilla_comunicacion')->getRutaPlantilla($plantilla);

        $data = file_get_contents($fichero);

        $response = new Response(
            'Content',
            200,
            ['content-type' => 'text/html']
        );

        $response->setContent($data);

        return $response;
    }
    
    
    
    public function accionExpImpPlantillaAction(Request $request)
    {
    	if(!$request->isXmlHttpRequest()) {
            return Response::create(sprintf('Método de acceso no válido'), 400);
        }

        $em = $this->getManager();

        $idComunicacion = $request->get('id_comunicacion');

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);
        $plantilla = $comunicacion->getPlantilla();



        if($request->get('accionEjecutar') == 'importar'){

            /*Se importa el id de la plantilla modelo a plantilla actual de comunicaciÃ³n, eliminando la existente*/
            //$respuesta = $servicioPlantilla->importarModeloPlantillaToActual($request->get('plantillaModelo'), $idPlantillaActual);
            $id = $request->get('plantillaModelo');
            $plantillaModelo =
                $em->getRepository('RMPlantillaBundle:Plantilla')->findOneBy([
                        'idPlantilla' => $id,
                        'esModelo' => true
                    ]);

            $comunicacion->setPlantilla($plantillaModelo);
            $plantilla->getEsModelo() == true? : $plantilla->setEstado(-1);


            $this->get('session')->getFlashBag()->add('mensaje','importar_plantilla_ok');
        }
        elseif ($request->get('accionEjecutar') == 'exportar') {

            $nombre = $request->get('nombre');
            $descripcion = $request->get('descripcion');

            $plantilla
                ->setEsModelo(true)
                ->setNombre($nombre)
                ->setDescripcion($descripcion)
                ->setCanal($comunicacion->getIdCanal())
            ;


            $this->get('session')->getFlashBag()->add('mensaje','exportar_plantilla_ok');

        }

        $em->persist($plantilla);
        $em->persist($comunicacion);
        $em->flush();

        return new JsonResponse();
    }
    
    public function descargarFicherosPlantillaAction($id_comunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($id_comunicacion);
        $plantilla = $comunicacion->getPlantilla();


        if (!$plantilla instanceof Plantilla) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        }


        $filePath = $this->get('rm_plantilla_genera_plantilla_comunicacion')->getRutaPlantilla($plantilla);

        if (!file_exists($filePath)) {
            $this->get('rm_plantilla_genera_plantilla_comunicacion')
                ->creaArchivoPlantilla($plantilla, $this->getUser()->getCliente());
        }


        $errores = $this->get('rm_plantilla_genera_plantilla_comunicacion')->compruebaPlantilla($plantilla);


        $filename =  $plantilla->getIdPlantilla().'.html';


        // check if file exists
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException();
        }

        // prepare BinaryFileResponse
        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->headers->set('Content-Type', 'text/plain');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
    
    public function uploadFicherosPlantillaAction($id_comunicacion, Request $request)
    {
    	$usuario = $this->get('security.context')->getToken()->getUser();
    	$folderName = $usuario->getCliente();  //Identificacion del centro
    	$myAssetUrl = $this->get('kernel')->getRootDir() . '/../web';
    	
    	//Recibe un fichero Html.
    	if ($request->isMethod('POST')) {
    		
    		//Se el fichero html con nombre de la id_comunicaci�n dentro de su centro correspondiente. Si no existe, se crea
            $em = $this->getManager();

            $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($id_comunicacion);
            $plantilla = $comunicacion->getPlantilla();

            $rutaPlantilla = $this->get('rm_plantilla_genera_plantilla_comunicacion')->getRutaPlantilla($plantilla);


    		$exito = true;
    		try {
    			$carpetaCentro = $myAssetUrl."/".$folderName;
	    		if(!file_exists($carpetaCentro)){
	    			mkdir($carpetaCentro);
	    		}
	    		
	    		$carpetaPlantilla = $carpetaCentro."/plantillas";
	    		if(!file_exists($carpetaPlantilla)){
	    			mkdir($carpetaPlantilla);
	    		}
	    		
	    		$carpetaFicPlantilla = $carpetaPlantilla."/". $id_comunicacion.".html";
	    		move_uploaded_file( $_FILES['fichero']['tmp_name'], $rutaPlantilla);

                $errores = $this->get('rm_plantilla_genera_plantilla_comunicacion')->compruebaPlantilla($plantilla);


                if(count($errores)){
                    $exito = false;
                }
	    		
	    		$this->get('ladybug')->log($carpetaFicPlantilla);    		
    			
    		} catch (\Exception $e) {
    			$exito = false;
    		}
    		
    		if($exito){
    			$this->get('session')->getFlashBag()->add('mensaje','editar_ok');
    		}
    		else{
    			//$this->get('session')->getFlashBag()->add('mensaje','error_general');
                throw new \Exception(sprintf('No se ha impotado la plantilla. Se han producido los siguientes errores: %s', implode('   ', $errores)));
    		}    		
    		
    		return $this->redirect($this->generateUrl('direct_manager_edit_plantillas', [
    			'id_comunicacion' => $id_comunicacion	
    		]));
    		
    	}
    	else{
    		throw $this->createNotFoundException('Se ha producido un error de envio de la informaciÃ³n');
    	}
    }

}
