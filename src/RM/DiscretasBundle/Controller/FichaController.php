<?php

namespace RM\DiscretasBundle\Controller;

use RM\DiscretasBundle\Entity\Tipo;
use RM\DiscretasBundle\Entity\Vid;
use RM\DiscretasBundle\Form\Ficha\ModificarGrupoNType;
use RM\DiscretasBundle\Form\Ficha\ModificarGrupoNyMType;
use RM\DiscretasBundle\Form\Ficha\ModificarGrupoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FichaController extends Controller
{
    public function fichaVariableDiscretaAction($idOpcionMenuSup, $idOpcionMenuIzq, $id_vid, $id_vid_grupo_segmento)
    {	
    	/*Parametros:
	    	id_vid_grupo_segmento: El id del grupo de segmento.
	    	id_vid: El id de la variable.
    	  Acción:
    		Muestra la ficha por defecto de una variable y la muestra con un grupo de segmento asociado. Este puede ser por defecto (si se le pasa un 0 a la variable),
    		creando uno en el caso de que no tenga, o uno relacionado; en cualquier caso se comprueba si la relación entre ambos es verdadera.
    	*/
    	
    	$servicio = $this->get("variablesDiscretas");

    	//Se comprueba si el id pasado corresponde con una variable
    	$variable = $servicio->getVDbyId($id_vid);
    	

    	if (!$variable) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        }
        
        if($id_vid_grupo_segmento === 0)
        { 
        	//Si es 0 (como al entrar por primera vez), se coge una por defecto y si no tiene se crea)
        	//Se comprueba para los casos de categorias y marcas, que si no existen alguna de ellas, se lanza una excepcion
        	if($variable[0]->getClasificacion() === Vid::CLASIFICACION_CATEGORIA) //Categoria
        	{
        		$servicioCat  = $this->get("categoriaService");
        		$objExisteCat = $servicioCat->getCategoriasPorNivel(1);
        		if (!$objExisteCat) {
        			throw $this->createNotFoundException('No hay ninguna categoria creada');
        		}
        	}
        	elseif ($variable[0]->getClasificacion() === Vid::CLASIFICACION_MARCA) //Marca
        	{
        		$servicioMarca   = $this->get("marcaService");
        		$objExisteMarcas = $servicioMarca->getMarcas();
        		if (!$objExisteMarcas) {
        			throw $this->createNotFoundException('No hay ninguna marca creada');
        		}
        	}

        	
        	$grupoSegmento = $servicio->getGSbyIdVid($variable[0]);
        }
       	else
       	{ 
       		//Se obtiene el grupo pasado, aunque antes hay que comprobar que dicho grupo existe y que pertenece al id_vid pasado
       		$grupoSegmento = $servicio->getGSbyIdGrupo($id_vid_grupo_segmento);
       		if (!$grupoSegmento) {
       			throw $this->createNotFoundException('No se encuentra el grupoSegmento asociado a la variable');
       		}
       		else{
       			if($grupoSegmento[0]->getIdVid()->getIdVid() !== $id_vid){
       				throw $this->createNotFoundException('El idVid del grupoSegmento no coincide con la Id de la variabel ');
       			}
       		}
       		
       		$grupoSegmento = $grupoSegmento[0];

       	}

        $objGrupoSeg = $grupoSegmento;

        /**
         * Variable que identifica si se ha enviado los segmentos globales por defecto en el personalizado
         * no guardados, para despues hacerle un tratamiento diferente a la hora de guardar.
         */

       	$segGlobalDefecto = 0;
       	if($grupoSegmento->getPersonalizado())
       	{
       		/**
       		 * Si la variable es personalizada
       		 * Se obtienen los segmentos del grupo Vid
       		 * 
       		 */
       		
       		$segmentos = $servicio->getSegmentosByIdGrupo($grupoSegmento->getIdVidGrupoSegmento());
       		if(sizeof($segmentos) === 0){
                $this->get('rm_discretas.personaliza_variable_discreta')->copiaSegmentosGlobalesA($grupoSegmento);
                $segmentos = $servicio->getSegmentosByIdGrupo($grupoSegmento->getIdVidGrupoSegmento());

       		}
       		$criterio = $grupoSegmento;
       	}
       	else if(!$grupoSegmento->getPersonalizado())
       	{
       		/**
       		 * Si la variable no es personalizada
       		 * Se obtienen los segmentos globales
       		 */
       		$segmentos         = $servicio->getSegmentosGlobales();
       		$criteriosGlobales = $servicio->getCriteriosGlobales();
       		$criterio          = $criteriosGlobales[0];
       		
       	}
       	else
       		throw $this->createNotFoundException('No se puede conocer si la variable es personalizada o no');

        $peticion = $this->get('request');
        
    	//Creación del formulario mediante clase
    	//*************************************
        if($grupoSegmento->getPersonalizado() && $variable[0]->getSolicitaTiempo() === Vid::SOLICITA_N)
            $formulario1 = $this->createForm(new ModificarGrupoType(), $criterio );
        elseif ($variable[0]->getSolicitaTiempo() === Vid::SOLICITA_N)
            $formulario1 = $this->createForm(new ModificarGrupoNType(), $criterio);
        elseif ($variable[0]->getSolicitaTiempo() === Vid::SOLICITA_N_M)
            $formulario1 = $this->createForm(new ModificarGrupoNyMType(), $criterio);
        else
            $formulario1 = null; // $this->createForm(new ModificarGrupoType(), $criterio);

        if ($peticion->getMethod() === 'POST') {

            $formulario1->handleRequest($peticion);

            if ($formulario1->isValid()) {
                //Se ha hecho pulsado sobre el botón de guardar correspondiente a la ficha del grupo de segmento
                $objGrupoSeg = $servicio->guardarObjeto($objGrupoSeg);

                if ($objGrupoSeg) {
                    $this->get('session')->getFlashBag()->add('mensaje', 'editar_ok');
                } else {
                    $this->get('session')->getFlashBag()->add('mensaje', 'error_general');
                }
            }
        }
        //*************************************
     	//Ahora se envia un array de objetos conteniendo las categorias,proveedores o marcas que tenga configurado la variable
     	$objClasificacion = NULL; //Es un objeto que contiene o las categorias o marcas, segun configuracion
     	if($variable[0]->getClasificacion() === Vid::CLASIFICACION_CATEGORIA) //Categoria
     	{	
     		$servicioCat      = $this->get("categoriaService");
     		$objClasificacion = $servicioCat->getCategoriasPorNivel(1);
     		
     	}
     	elseif ($variable[0]->getClasificacion() === Vid::CLASIFICACION_PROVEEDOR) //Proveedor
     	{
     		$servicioProveedor     = $this->get("ProveedorService");
     		$objClasificacion      = $servicioProveedor->getProveedores();
            $objClasificacion = $objClasificacion[0];
     	}
     	elseif ($variable[0]->getClasificacion() === Vid::CLASIFICACION_MARCA) //Marca
     	{	
     		$servicioMarca    = $this->get("marcaService");
     		$objClasificacion = $servicioMarca->getMarcas();
     	}
     	
     	//$this->get('ladybug')->log($formulario1->createView()->vars['value']);
        if($formulario1 !== null){
            $formulario1 = $formulario1->createView();
        }
     	
    	return $this->render('RMDiscretasBundle:Ficha:index.html.twig', [
    			'idOpcionMenuSup'  => $idOpcionMenuSup,
    			'idOpcionMenuIzq'  => $idOpcionMenuIzq,
    			'formGrupo'        => $formulario1,
    			'segGlobalDefecto' => $segGlobalDefecto,
    			'objVariable'      => $variable[0],
    			'objGrupo'         => $objGrupoSeg,
    			'objSegmentos'     => $segmentos,
    			'objClasificacion' => $objClasificacion
    	]);
    	 
    }
    
    public function mostrarSegmentosAsocAction($idNuevoSeg){
    	/*Parametros principales:
	    	$idNuevoSeg: contador numerico
	      Acci�n:
    		Crea una nueva fila de segmento asociado a un grupo de segmento, poniendo como parte de los ids al numero pasado
    	*/
    	
    	return $this->render('RMDiscretasBundle:Ficha:nuevaFilaSegmento.html.twig', [
    		'idNuevoSeg' => $idNuevoSeg
    	]);
    }
        
    public function eliminarGuardarSegmentosAsocAction(Request $request){
    	/*Parametros principales:
	    	accionEjecutar: Realiza la operacion de guardar o de eliminar
	    	listaSegmentosAEliminar: En el caso de eliminar, lista de ids separados por , de los segmentos a eliminar
	    	contSegmentos: Numero de segmentos nuevos a crear
	    	numSegAct: Numero de segmentos asociados actuales que tiene el grupo de segmento
	    	id_vid: El id de la variable.
	    	id_vid_grupo_segmento: El id del grupod de segmento.
	      Acci�n:
	    	Elimina o guarda y/o modifica los segmentos asociados a un grupo de segmentos de una variables
	    */
    	if ($request->isMethod('POST')) {
    		$servicio = $this->get("variablesDiscretas");
    		if($request->get('accionEjecutar') === 'eliminar'){
    			$deleteSeg = $servicio->eliminarSegmentosAsocByIdSegmento($request->get('listaSegmentosAEliminar'));
    			
    			$this->get('session')->getFlashBag()->add('mensaje','eliminar_ok');    		
    		}
    		elseif ($request->get('accionEjecutar') === 'guardar'){
    			//Se a�aden los nuevos segmentos en el caso de que halla
    			if($request->get('segGlobalDefecto') === 1){
    				$guardarSeg = $servicio->guardarNuevosSegmentosGlobalesDefectoAsocByPost($request);
    			}
    			if($request->get('contSegmentos') > 0){
    				$guardarSeg = $servicio->guardarNuevosSegmentosAsocByPost($request);
    			}
    			if($request->get('numSegAct') > 0 && $request->get('segGlobalDefecto') === 0){
    				$modificarSeg = $servicio->modificarSegmentosAsocByPost($request);
    			}
    			
    			$this->get('session')->getFlashBag()->add('mensaje','editar_ok');
    		}
    		
    		$objVar = $servicio->getVDbyId($request->get('id_vid'));
    		$objVid = $objVar[0];
    		
    		if($objVid->getTipo()->getCodigo() === Tipo::COMPRA_PRODUCTO){
	    		return $this->redirect($this->generateUrl('data_avanced_cp_editar_con_grupo', [
	    				'id_vid'                => $request->get('id_vid'),
	    				'id_vid_grupo_segmento' => $request->get('id_vid_grupo_segmento')
	    		]));
    		}
	    	else{
	    		return $this->redirect($this->generateUrl('data_avanced_hc_editar_con_grupo', [
	    				'id_vid'                => $request->get('id_vid'),
	    				'id_vid_grupo_segmento' => $request->get('id_vid_grupo_segmento')
	    		]));
	    	}
    	}
    	else{
    		throw $this->createNotFoundException('Se ha producido un error de envio de la informaci�n');
    	}
    }

    public function devolverGrupoSegmentoAction(Request $request){
    	/*Parametros:
    	 	tipoClasficacion: 1- Si es Categoria, 2- Si es Marca
    	 	idClasificacion: El id de la categoria o marca escogida
    	 	id_vid: El id de la variable.
    	 Acci�n:
    	 	Encontrar un grupo segmento para dicha configuraci�n. En el caso de que no exista crearlo.
    	 	Por ultimo, redireccionar a la ficha de la variable con el (nuevo) grupo de segmento.
    	 */
    	if ($request->isMethod('POST')) {
    		$servicioVD  = $this->get("variablesDiscretas");
    		$objGrupoSeg = null;

    		if($request->get('tipoClasificacion') === Vid::CLASIFICACION_CATEGORIA){
    			$objGrupoSeg = $servicioVD->getGSByIdVidIdCategoria($request->get('id_vid'), $request->get('idClasificacion'));
    		}
    		elseif($request->get('tipoClasificacion') === Vid::CLASIFICACION_MARCA){
    			$objGrupoSeg = $servicioVD->getGSByIdVidIdMarca($request->get('id_vid'), $request->get('idClasificacion'));
    		}
            elseif($request->get('tipoClasificacion') === Vid::CLASIFICACION_PROVEEDOR){
                $objGrupoSeg = $servicioVD->getGSByIdVidIdProveedor($request->get('id_vid'), $request->get('idClasificacion'));
            }
    		
    		$objVar = $servicioVD->getVDbyId($request->get('id_vid'));
    		$objVid = $objVar[0];
    		
    		if(!$objGrupoSeg){
    			//Si no hay ninguno, se crea uno nuevo. Se obtiene los objetos tanto de la categoria como de la variable
    			if($request->get('tipoClasificacion') === Vid::CLASIFICACION_CATEGORIA){
	    			$servicioCat  = $this->get("categoriaService");
	    			$objCat       = $servicioCat->getCatById($request->get('idClasificacion'));
	    			$objCategoria = $objCat[0];
	    			$objGS        = $servicioVD->crearObjGrupoSegmentoConCategoria($objVid, $objCategoria, $request->get('personalizado'));
    			}
    			elseif($request->get('tipoClasificacion') === Vid::CLASIFICACION_MARCA ){
    				$servicioMarca = $this->get("marcaService");
    				$objMar        = $servicioMarca->getMarcaById($request->get('idClasificacion'));
    				$objMarca      = $objMar[0];
    				$objGS         = $servicioVD->crearObjGrupoSegmentoConMarca($objVid, $objMarca);
    			}
                elseif($request->get('tipoClasificacion') === Vid::CLASIFICACION_PROVEEDOR){
                    $objProveedor = $this->get('proveedorservice')->getProveedorById($request->get('idClasificacion'));
                    $objProveedor =$objProveedor[0];
                    $objGS        = $servicioVD->crearObjGrupoSegmentoConProveedor($objVid, $objProveedor);
                }
    		}
    		else{
    			$objGS = $objGrupoSeg[0];
    		}
    		
    		if($objVid->getTipo()->getCodigo() === Tipo::COMPRA_PRODUCTO){
    			return $this->redirect($this->generateUrl('data_avanced_cp_editar_con_grupo', [
    					'id_vid'                => $request->get('id_vid'),
    					'id_vid_grupo_segmento' => $objGS->getIdVidGrupoSegmento()
    			]));
    		}	
    		else {
    			return $this->redirect($this->generateUrl('data_avanced_hc_editar_con_grupo', [
    					'id_vid'                => $request->get('id_vid'),
    					'id_vid_grupo_segmento' => $objGS->getIdVidGrupoSegmento()
    			]));
    		}
    		
    	}
    	else{
    		throw $this->createNotFoundException('Se ha producido un error de envio de la información');
    	}
    }
    
    public function showCopiarSegmentosAVariablesAction($id_vid, $id_vid_grupo_segmento){
    	/*Parametros:
	    	id_vid: El id de la variable.
	    	id_vid_grupo_segmento: El id del grupo de segmento.
    	Acci�n:
	    	Muestra la pantalla de copiado de segmentos a otras variables. Se envia la lista de segmentos asociados
	    	al grupo origen y la lista de todas las variables de su tipo
    	*/
    	
    	$servicio = $this->get("variablesDiscretas");
    	
    	//Se comprueba si el id pasado corresponde con una variable
    	$variable = $servicio->getVDbyId($id_vid);
    	 
    	if (!$variable) {
    		throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
    	}
    	else{
    		$objVar = $variable[0];
    	}
    	
    	$grupoSegmento = $servicio->getGSbyIdGrupo($id_vid_grupo_segmento);
    	if (!$grupoSegmento) {
    		throw $this->createNotFoundException('No se encuentra el elemento seleccionado de la variable');
    	}
    	else{
    		if($grupoSegmento[0]->getIdVid()->getIdVid() !== $id_vid){
    			throw $this->createNotFoundException('No se encuentra el elemento de la variable seleccionada');
    		}
    	}
    	$objGS = $grupoSegmento[0];
    	
    	//Se obtiene los segmentos asociados a dicho grupo
    	$objSegmentos = $servicio->getSegmentosByIdGrupo($objGS->getIdVidGrupoSegmento());
    	
    	$objsVariables = $servicio->getDiscretas('', $objVar->getTipo());
    	
    	
    	return $this->render('RMDiscretasBundle:Ficha:copiarSegmentos.html.twig', [
    			'objVar'       => $objVar,
    			'objGrupo'     => $objGS,
    			'objSegmentos' => $objSegmentos,
    			'objVariables' => $objsVariables
    	]);
    	
    	
    }
    
    public function actualizarPersonalizadoAction(Request $request)
    {
    	$servicio = $this->get('variablesdiscretas');

        $objVid = $servicio->getVDbyId($request->get('id_vid'))[0];

        if(!$objVid instanceof Vid) {
            $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.error.personalizar');
            $this->redirect($this->getRefererRoute());
        }

    	//Se obtiene si se ha marcado o no como personalizada
    	$personalizada = $request->get('personalizado');

    	
    	//Se obtiene el grupo de la variable
    	$objGrupoSegmento = $servicio->getGSbyIdVid($objVid);

        $personaliza = $this->get('rm_discretas.personaliza_variable_discreta');
    	if(!$personalizada) {
    		$respuesta  = $personaliza->despersonalizarVariable($objVid);
    	}
        elseif($personalizada) {
            $respuesta = $personaliza->personalizaVariable($objVid);
    	}

        if(!$respuesta) {
            $this->get('session')->getFlashBag()->add('mensaje', 'mensaje.error.personalizar');
        }
    	
    	if($objVid->getTipo()->getCodigo() === Tipo::COMPRA_PRODUCTO){
    			return $this->redirect($this->generateUrl('data_avanced_cp_editar_con_grupo', [
    					'id_vid'                => $request->get('id_vid'),
    					'id_vid_grupo_segmento' => $objGrupoSegmento->getIdVidGrupoSegmento()
    			]));
    		}	
    		else {
    			return $this->redirect($this->generateUrl('data_avanced_hc_editar', [
    					'id_vid' => $request->get('id_vid'),
    			]));
    		}
    	
    }

    public function ajaxProveedoresAction()
    {
       $term =  $this->get('request')->get('term', '');

        $servicioProveedor = $this->get("ProveedorService");

        $proveedores = $servicioProveedor->findProvedoresByNombre($term);

        $json = [];

        /**
         * @var $proveedor \RM\ProductoBundle\Entity\Proveedor
         */
        foreach($proveedores as  $proveedor){
            $json[] = [
                'id'    => $proveedor->getIdProveedor(),
                'label' => $proveedor->getNombre(),
                'value' => $proveedor->getNombre()
            ];
        }

        return new JsonResponse($json);

    }

    private function getRefererRoute()
    {
        $request = $this->getRequest();

        //look for the referer route
        $referer  = $request->headers->get('referer');
        $lastPath = substr($referer, strpos($referer, $request->getBaseUrl()));
        $lastPath = str_replace($request->getBaseUrl(), '', $lastPath);

        $matcher    = $this->get('router')->getMatcher();
        $parameters = $matcher->match($lastPath);
        $route      = $parameters['_route'];

        return $route;
    }
}
