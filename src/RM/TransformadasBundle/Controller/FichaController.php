<?php

namespace RM\TransformadasBundle\Controller;


use RM\DiscretasBundle\Entity\Tipo;
use RM\TransformadasBundle\Entity\Vt;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class FichaController extends Controller
{
    public function fichaVariableTransAction($idOpcionMenuSup, $idOpcionMenuIzq, $tipoVar, $id_vt)
    {
    	/*Parametros:
    	id_vt: El id de la variable.
    	tipoVar: El id tipo de la variable.
    	Acci�n:
    	Muestra la ficha de una variable . En el caso de que no tenga ningun segmento relacionado, se le creara uno por defecto, asi como un grupo
    	relacionado. La variable intermedia o lineal se guardar� una vez pulsado el bot�n de guardar. En caso contrario, mostrara todas sus relaciones.
    	*/
    	 
    	$servicio = $this->get("variablesTransformadas");
    	$servicioVL = $this->get("variablesLineales");
    	
    	//Se comprueba si el id pasado corresponde con una variable
    	$objVar = $servicio->getVTbyId($id_vt);
        $objVar = is_array($objVar) ? $objVar[0] : $objVar;
    	 
    	if (!$objVar && $objVar->getTipo() != $tipoVar) {
    		throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
    	}
    	
    	$objSegmentos = $servicio->getSegmentosByIdVt($id_vt);
    	$objGrupos = $servicio->getGruposByIdVt($id_vt);
    	$objIntervalos = $servicio->getIntervalosByIdVt($id_vt);

        $otras_transformadas = $objVar->getTipo()->getCodigo() == Tipo::OTRAS_TRANSFORMADAS ? true : false;
    	
    	/*Se obtiene los rowspan de los grupos y de los segmentos
    	 * Estara formado por: g_id y s_id, haciendo refrencia al grupo mas su id y al segmento mas su id. Esto sera la clave para obtener su valor*/
    	$arrayRowspan = $servicio->getArrayRowSpanSegmentosGruposVT($id_vt);
    	
    	$objVL = $servicioVL->getVariablesLinealesNoSociodemograficas();
    	
    	return $this->render('RMTransformadasBundle:Ficha:index.html.twig', array(
    			'idOpcionMenuSup' => $idOpcionMenuSup,
    			'idOpcionMenuIzq' => $idOpcionMenuIzq,
    			'arrayRowspan' => $arrayRowspan,
    			'objVariable' => $objVar,
    			'objVL' => $objVL,
    			'objSegmentos' => $objSegmentos,
    			'objGrupos' => $objGrupos,
    			'objIntervalos' => $objIntervalos,
                'otrasTransformadas' => $otras_transformadas
    	));
    	 
    }
    
    public function eliminarGuardarSegmentosAsocAction(Request $request){
    	/*Parametros principales:
    	 	accionEjecutar: Realiza la operacion de guardar o de eliminar
    		listaSegmentosAEliminar: En el caso de eliminar, lista de ids separados por , de los segmentos a eliminar
    		listaGruposAEliminar: En el caso de eliminar, lista de ids separados por , de los grupos a eliminar
    		listaIntervalosAEliminar: En el caso de eliminar, lista de ids separados por , de los intervalos a eliminar
	    	id_vt: El id de la variable transformada.
    	 Acci�n:
    		Elimina o guarda y/o modifica los segmentos asociados junto a sus grupos e intervalos.
    	*/
    	if ($request->isMethod('POST')) {
    		$servicio = $this->get("variablesTransformadas");
    		if($request->get('accionEjecutar') == 'eliminar'){
                //TODO arreglar esta parte de guardar y eliminar segmentos
                //Se ha copiado la parte de guardar, para mantener los posibles cambios que se hayan realizado.
                $guardarSeg = $servicio->guardarSegGrupoVIbyPost($request);

                if($guardarSeg){
                    $this->get('session')->getFlashBag()->add('mensaje','editar_ok');
                }
                else{
                    $this->get('session')->getFlashBag()->add('mensaje','error_general');
                }

                //despues de guardar los cambios se eliminan los grupos.
    			$deleteSeg = $servicio->eliminarSegGrupoVIbyPost($request->get('listaSegmentosAEliminar'), $request->get('listaGruposAEliminar'), $request->get('listaIntervalosAEliminar'), $request->get('id_vt'));
    		
    			if($deleteSeg){
    				$this->get('session')->getFlashBag()->add('mensaje','eliminar_ok');
    			}
    			else{
    				$this->get('session')->getFlashBag()->add('mensaje','error_general');
    			}
    		}
    		elseif ($request->get('accionEjecutar') == 'guardar'){
    			$guardarSeg = $servicio->guardarSegGrupoVIbyPost($request);
    			
    			if($guardarSeg){
    				$this->get('session')->getFlashBag()->add('mensaje','editar_ok');
    			}
    			else{
    				$this->get('session')->getFlashBag()->add('mensaje','error_general');
    			}
    		}    
    		$objVar = $servicio->getVTbyId($request->get('id_vt'));
    		$objVt = $objVar[0];
    
    		if($objVt->getTipo()->getId() == Vt::TIPO_CICLO_VIDA){
    			return $this->redirect($this->generateUrl('data_avanced_cv_editar', array(
    					'id_vt' => $request->get('id_vt')
    			)));
    		}
    		else{
    			return $this->redirect($this->generateUrl('data_avanced_ot_editar', array(
    					'id_vt' => $request->get('id_vt')
    			)));
    		}
    	}
    	else{
    		throw $this->createNotFoundException('Se ha producido un error de envio de la informaci�n');
    	}
    }
    
    public function nuevoSegmentoVTAction($idNuevoSeg, $id_vt){
    	/*Parametros principales:
    	 	idNuevoSeg: Sirve para nombrar la numeraci�n de los segmentos en la tabla
    	
    		id_vt: El id de la variable transformada.
    	 Acci�n:
    		Se crean 3 objetos: un segmento, un grupo asociado y un intervalo asociado. Todos ellos, se pasan por parametro luego para 
    		crear los ids de los elementos con los ids creados en bbdd. Esto representar� una linea entera de la tabla
    	*/
    	
    	//$translated = $this->get('translator')->trans('Symfony2 is great');
    	//ECHO 'ENTRO EN NUEVOSEGMENTOVT-';
    	$servicioVL = $this->get("variablesLineales");
    	$objVL = $servicioVL->getVariablesLinealesNoSociodemograficas();
    	
    	//ECHO '-OBJETO VARIABLES LINEALES-';
    	//var_dump($objVL);
    	
    	$servicioVT = $this->get("variablesTransformadas");
    	
    	$nombreSeg =  $this->get('translator')->trans('Segmento'). " ". $idNuevoSeg;
//     	ECHO '-NOMBRE SEG-';
//     	var_dump($nombreSeg);
//     	ECHO '-VALOR IDVT-';
//     	var_dump($id_vt);
    	$objSegmento = $servicioVT->crearObjSegmento($nombreSeg, $id_vt);
//     	ECHO '-OBJSEGMENTO-';
//     	var_dump($objSegmento);
    	
    	
    	$nombreGrupo =  $this->get('translator')->trans('Nuevo Grupo');
//     	ECHO 'NOMBRE GRUPO';
    	//var_dump($nombreGrupo);
    	$objGrupo = $servicioVT->crearObjGrupo($nombreGrupo, $objSegmento->getIdVtSegmento());
//     	ECHO 'OBJETO GRUPO';
    	//var_dump($objGrupo);
    	$id_vil = $objVL[0]->getIdVil();
    	$id_grupo = $objGrupo->getIdGrupo();
    	$objIntervalo = $servicioVT->crearObjIntervalo($id_grupo, $id_vil);
    	
    	
    	return $this->render('RMTransformadasBundle:Ficha:filaNuevoSegmento.html.twig', array(
    			'idNuevoSeg' => $idNuevoSeg,
    			'objSegmento' => $objSegmento,
    			'objGrupo' => $objGrupo,
    			'objIntervalo' => $objIntervalo,
    			'objVL' => $objVL
    	));
    }
    
    public function parteFilaTablaSegmentoVTAction($idSeg){
    	/*Parametros principales:
    	 	$idSeg: id del segmento desde el que se ha invocado
    	Acci�n:
    		Se crean 2 objetos: un grupo asociado al id del segmento que se pasa por parametro y un intervalo asociado a este. Todos ellos, se pasan por parametro luego para
    		crear los ids de los elementos con los ids creados en bbdd. Esto representar� una nueva rama del segmento que hemos elegido.
    	*/
    	$servicioVL = $this->get("variablesLineales");
    	$objVL = $servicioVL->getVariablesLinealesNoSociodemograficas();
    	
    	$servicioVT = $this->get("variablesTransformadas");
    	
    	$nombreGrupo =  $this->get('translator')->trans('Nuevo Grupo');
    	$objGrupo = $servicioVT->crearObjGrupo($nombreGrupo, $idSeg);
    	 
    	$id_vil = $objVL[0]->getIdVil();
    	$id_grupo = $objGrupo->getIdGrupo();
    	$objIntervalo = $servicioVT->crearObjIntervalo($id_grupo, $id_vil);
    	
    	
    	return $this->render('RMTransformadasBundle:Ficha:parteAddSegmento.html.twig', array(
    			'idSeg' => $idSeg,
    			'objGrupo' => $objGrupo,
    			'objIntervalo' => $objIntervalo,
    			'objVL' => $objVL
    	));
    }
    
    public function parteFilaTablaGrupoVTAction($idGrupo){
    	/*Parametros principales:
    	 	$idGrupo: id del grupo desde el que se ha invocado
    	Acci�n:
    		Se crea el objeto intervalo a partir del grupo del que se ha invocado. Se pasar� por parametro para
    		crear los ids de los elementos con los ids creados en bbdd. Esto representar� una nueva rama del grupo que hemos elegido.
    	*/
    	$servicioVL = $this->get("variablesLineales");
    	$objVL = $servicioVL->getVariablesLinealesNoSociodemograficas();
    	
    	$servicioVT = $this->get("variablesTransformadas");
    	
    	$id_vil = $objVL[0]->getIdVil();
    	$objIntervalo = $servicioVT->crearObjIntervalo($idGrupo, $id_vil);
    	
    	$objGrupo = $servicioVT->getGrupoById($idGrupo);
    	
    	return $this->render('RMTransformadasBundle:Ficha:parteAddGrupo.html.twig', array(
    			'objIntervalo' => $objIntervalo,
    			'objVL' => $objVL,
    			'objGrupo' => $objGrupo[0]
    	));
    }
    
}