<?php

namespace RM\ComunicacionBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;



class CreatividadController extends RMController
{

    //FUNCIONES DE GESTION DE CREATIVIDADES DE LA PARTE DE DIRECT

    public function showCreatividadAction($idOpcionMenuSup, $idOpcionMenuIzq, $opcionMenuTabConfig, $nombre = "") {
        $servicioCR = $this->get ( "CreatividadService" );


        $extensionFormatoImages = [".jpeg",".jpg",".gif",".tiff",".bmp",".png"];

        $paginator = $this->get ( 'ideup.simple_paginator' );
        $paginator->setItemsPerPage ( 25 ); // Para poner el numero de item que se quieren por pagina

        $selectCreatividad = $paginator->paginate( $servicioCR->getCreatividadByFiltroDQL($nombre))->getResult ();

        return $this->render ( 'RMComunicacionBundle:Creatividad:index.html.twig', [
            'idOpcionMenuSup' => $idOpcionMenuSup,
            'idOpcionMenuIzq' => $idOpcionMenuIzq,
            'opcionMenuTabConfig' => $opcionMenuTabConfig,
            'objCreatividades' => $selectCreatividad,
            'extensionFormatoImages' => $extensionFormatoImages,
            'nombre' => $nombre
        ] );
    }


    public function actualizarCreatividadAction() {
        if ($this->container->get ( 'request' )->isXmlHttpRequest ()) {
            $request = $this->container->get ( 'request' );
            $servicioCR = $this->get ( "CreatividadService" );

            $extensionFormatoImages = [".jpeg",".jpg",".gif",".tiff",".bmp",".png"];

            $paginator = $this->get ( 'ideup.simple_paginator' );
            $paginator->setItemsPerPage ( 25 ); // Para poner el numero de item que se quieren por pagina

            $selectCreatividad = $paginator->paginate ( $servicioCR->getCreatividadByFiltroDQL ( $request->get ( 'nombre' ) ) )->getResult ();

            return $this->render ( 'RMComunicacionBundle:Creatividad:listadoCreatividad.html.twig', [
                'extensionFormatoImages' => $extensionFormatoImages,
                'objCreatividades' => $selectCreatividad,
                'nombre' => $request->get ( 'nombre' )
            ] );
        } else {
            throw $this->createNotFoundException ( 'Se ha producido un error de envio de la informaciï¿½n' );
        }
    }

    public function informacionCreatividadAction($idOpcionMenuSup, $idOpcionMenuIzq, $idInstancia){

        $em = $this->getManager();

        $instancia = $em->find('RMComunicacionBundle:InstanciaComunicacion', $idInstancia);

        if(!$instancia instanceof InstanciaComunicacion){
            throw $this->createNotFoundException('No se ha encontrado instancia de comunicación');
        }

        $creatividadService = $this->get('creatividadservice');

        $creatividades = $creatividadService->getDatosPromocionesCreatividadByInstancia($instancia);

        return $this->render(
            'RMComunicacionBundle:Campaign\Creatividades:fichaCreatividad2.html.twig',
            [
                'objInstancia' => $instancia,
                'creatividades' => $creatividades,
                'idOpcionMenuSup' => $idOpcionMenuSup,
                'idOpcionMenuIzq' => $idOpcionMenuIzq
            ]
        );
    }


    public function showFichaCreatividadCrearAction() {
        $estado = 'new';

        return $this->render ( 'RMComunicacionBundle:Creatividad:fichaCreatividad.html.twig', [
            'creation' => $estado,
            'selectedCreatividad' => null
        ] );
    }

    public function uploadImageCreatividadAction() {

        $request = $this->container->get( 'request' );
        $servicioCR = $this->get( "CreatividadService" );

        $usuario = $this->get('security.context')->getToken()->getUser();
        $folderName = $usuario->getCliente();  //Identificacion del centro
        $myAssetUrl = $this->get('kernel')->getRootDir() . '/../web';

        //Recibe una imagen y el formulario de una creatividad nueva en el caso del parámetro de creation.
        if ($request->isMethod('POST')) {

            //Pantalla de creación. Se guarda el objeto
            $exito = true;
            if($request->get('creation') == 'new'){
                $objCreatividad = $servicioCR->crearCreatividad($request->get('nombre'), $request->get('descripcion'));
                $id_creatividad = $objCreatividad->getIdCreatividad();
            }
            else{
                $id_creatividad = $request->get( 'id_creatividad' );
            }

            //Guardado de la imagen
            if($exito) {
                try {
                    $carpetaCentro = $myAssetUrl."/".$folderName;
                    if(!file_exists($carpetaCentro)){
                        mkdir($carpetaCentro);
                    }

                    $carpetaPlantilla = $carpetaCentro."/imagenesCreatividad";
                    if(!file_exists($carpetaPlantilla)){
                        mkdir($carpetaPlantilla);
                    }

                    $arrayExt = explode(".", basename($_FILES ["uploadFile"] ["name"]));
                    $extension = $arrayExt[1];
                    $carpetaFicPlantilla = $carpetaPlantilla."/". $id_creatividad.".".$extension;

                    if(!move_uploaded_file( $_FILES['uploadFile']['tmp_name'], $carpetaFicPlantilla)){
                        $exito = false;
                    }

                } catch (\Exception $e) {
                    $exito = false;
                }
            }

            if($exito){
                $this->get('session')->getFlashBag()->add('mensaje','editar_ok');
            }
            else{
                $this->get('session')->getFlashBag()->add('mensaje','error_general');
            }

            return $this->render ( '::logMensajes.html.twig' );
        }
        else{
            throw $this->createNotFoundException('Se ha producido un error de envio de la información');
        }
    }

    public function searchCreatividadesPopoupAction($nombre = "") {

        $request = $this->container->get ( 'request' );
        $servicioCR = $this->get ( "CreatividadService" );

        $extensionFormatoImages = [".jpeg",".jpg",".gif",".tiff",".bmp",".png"];

        $paginator = $this->get ( 'ideup.simple_paginator' );
        $paginator->setItemsPerPage ( 25 ); // Para poner el numero de item que se quieren por pagina

        $selectCreatividad = $paginator->paginate( $servicioCR->getCreatividadByFiltroDQL($nombre))->getResult ();

        return $this->render ( 'RMComunicacionBundle:Creatividad:buscadorCreatividadPopup.html.twig', [
            'id_id' => $request->get ( 'id_id' ),
            'id_nombre' => $request->get ( 'id_nombre' ),
            'objCreatividades' => $selectCreatividad,
            'extensionFormatoImages' => $extensionFormatoImages,
            'nombre' => $nombre
        ] );


    }

    public function searchActualizarCreatividadesPopupAction() {

        if ($this->container->get ( 'request' )->isXmlHttpRequest ()) {
            $request = $this->container->get ( 'request' );
            $servicioCR = $this->get ( "CreatividadService" );

            $extensionFormatoImages = [".jpeg",".jpg",".gif",".tiff",".bmp",".png"];

            $paginator = $this->get ( 'ideup.simple_paginator' );
            $paginator->setItemsPerPage ( 25 ); // Para poner el numero de item que se quieren por pagina

            $selectCreatividad = $paginator->paginate ( $servicioCR->getCreatividadByFiltroDQL ( $request->get ( 'nombre' ) ) )->getResult ();

            return $this->render ( 'RMComunicacionBundle:Creatividad:buscadorCreatividadPopupListado.html.twig', [
                'objCreatividades' => $selectCreatividad,
                'extensionFormatoImages' => $extensionFormatoImages,
                'nombre' => $request->get ( 'nombre' ),
            ] );

        } else {
            throw $this->createNotFoundException ( 'Se ha producido un error de envio de la información' );
        }
    }


    public function listaCreatividadesAction ($idOpcionMenuSup, $idOpcionMenuIzq)
    {

        $servicioIC = $this->get('InstanciaComunicacionService');


        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage(25); // Para poner el numero de item que se quieren por pagina

        $objInstancias = $paginator->paginate($servicioIC->getInstanciasCreatividad())->getResult();

        return $this->render(
            'RMComunicacionBundle:Campaign\Creatividades:listadoCreatividades.html.twig',
            [
                'idOpcionMenuSup' => $idOpcionMenuSup,
                'idOpcionMenuIzq' => $idOpcionMenuIzq,
                'objInstancias' => $objInstancias
            ]
        );
    }

    public function showFichaCreatividadAction($id_creatividad) {
        $servicioCR = $this->get ( "CreatividadService" );

        $selectedCreatividad = $servicioCR->getCreatividadById($id_creatividad);

        return $this->render ( 'RMComunicacionBundle:Creatividad:fichaCreatividad.html.twig', [
                'creation' => "old",
                'selectedCreatividad' => $selectedCreatividad [0]
            ] );
    }

    public function saveFichaCreatividadAction ()
    {

        $request = $this->container->get('request');
        $creatividades = $request->request->get('creatividad');
        $id_instancia = $request->request->get('id_instancia');

        $servicioCR = $this->get("CreatividadService");

        if($servicioCR->guardarPromocionesCreatividad($creatividades)) {
            $this->get('session')->getFlashBag()->add('mensaje', 'editar_ok');
        }
        else {
            $this->get('session')->getFlashBag()->add('mensaje', 'error_general');
        }

        return $this->redirect(
            $this->generateUrl(
                'campaign_creatividad_ficha',
                [
                    'idInstancia' => $id_instancia
                ]
            )
        );
    }

}
