<?php

namespace RM\PlantillaBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\PlantillaBundle\Entity\Plantilla;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DefaultController extends RMController
{

    public function importarPlantillaAction($id_canal, $id_comunicacion)
    {

        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($id_comunicacion);


        if ($comunicacion->getIdCanal()->getIdCanal() != $id_canal) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        }

        $plantillas = $em
            ->getRepository('RMPlantillaBundle:Plantilla')
            ->findPlantillasModeloByCanal($id_canal);

        return $this->render('RMPlantillaBundle:Default:importarPlantilla.html.twig', [
            'id_comunicacion' => $id_comunicacion,
            'objComunicacion' => $comunicacion,
            'objPlantillas'   => $plantillas
        ]);


    }

    public function exportarPlantillaAction($id_canal, $id_comunicacion)
    {
        $em = $this->getManager();


        $comunicacion = $em
            ->getRepository('RMComunicacionBundle:Comunicacion')
            ->findById($id_comunicacion);

        if (!$comunicacion instanceof Comunicacion) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        }

        if ($comunicacion->getIdCanal()->getIdCanal() != $id_canal) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        }

        return $this->render('RMPlantillaBundle:Default:exportarPlantilla.html.twig', [
            'objComunicacion' => $comunicacion
        ]);

    }

    //Leemos el fichero y devolvemos el contenido
    public function previsualizarPlantillaAction($id_comunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($id_comunicacion);

        $plantilla = $comunicacion->getPlantilla();

        $fichero = $this
            ->get('rm_plantilla.plantilla_checker')
            ->getRutaPlantilla($plantilla);

        if (!file_exists($fichero)) {
            return $this->render('@RMPlantilla/Default/error_fichero_plantilla.html.twig', [
                'error' => $this->get('translator')->trans('mensaje.error.descargar.fichero.plantilla')
            ]);
        }

        $errores = $this
            ->get('rm_plantilla.plantilla_checker')
            ->check($plantilla);

        if (count($errores) > 0) {
            return $this->render('@RMPlantilla/Default/error_fichero_plantilla.html.twig', [
                'error' => 'Plantilla obsoleta'
            ]);
        }


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
        if (!$request->isXmlHttpRequest()) {
            return Response::create(sprintf('Método de acceso no válido'), 400);
        }

        $em = $this->getManager();

        $idComunicacion = $request->get('id_comunicacion');

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($idComunicacion);
        $plantilla    = $comunicacion->getPlantilla();


        if ($request->get('accionEjecutar') == 'importar') {

            /*Se importa el id de la plantilla modelo a plantilla actual de comunicaciÃ³n, eliminando la existente*/
            //$respuesta = $servicioPlantilla->importarModeloPlantillaToActual($request->get('plantillaModelo'), $idPlantillaActual);
            $id              = $request->get('plantillaModelo');
            $plantillaModelo =
                $em->getRepository('RMPlantillaBundle:Plantilla')->findOneBy([
                    'idPlantilla' => $id,
                    'esModelo'    => true
                ]);

            $comunicacion->setPlantilla($plantillaModelo);
            $plantilla->getEsModelo() === true ?: $plantilla->setEstado(-1);


            $this->get('session')->getFlashBag()->add('mensaje', 'importar_plantilla_ok');
        } elseif ($request->get('accionEjecutar') == 'exportar') {

            $nombre      = $request->get('nombre');
            $descripcion = $request->get('descripcion');

            $plantilla
                ->setEsModelo(true)
                ->setNombre($nombre)
                ->setDescripcion($descripcion)
                ->setCanal($comunicacion->getIdCanal());


            $this->get('session')->getFlashBag()->add('mensaje', 'exportar_plantilla_ok');

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
        $plantilla    = $comunicacion->getPlantilla();


        if (!$plantilla instanceof Plantilla) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        }

        $filePath = $this
            ->get('rm_plantilla_genera_plantilla_comunicacion')
            ->getRutaPlantilla($plantilla);

        if (!file_exists($filePath)) {
            $this->addFlash('error_popup',
                $this->get('translator')->trans('mensaje.error.descargar.fichero.plantilla'));

            return $this->redirectToRoute('rm_comunicacion.comunicacion.editar_plantilla', [
                'idComunicacion' => $id_comunicacion
            ]);
        }

        $filename = $plantilla->getIdPlantilla() . '.html';


        // prepare BinaryFileResponse
        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->headers->set('Content-Type', 'text/html');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }

    public function generarYDescargarAction(Request $request, $id_comunicacion)
    {
        $em = $this->getManager();

        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($id_comunicacion);
        $plantilla    = $comunicacion->getPlantilla();


        if (!$plantilla instanceof Plantilla) {
            throw $this->createNotFoundException('No se ha encontrado la variable solicitada');
        }

        $filePath = $this
            ->get('rm_plantilla_genera_plantilla_comunicacion')
            ->creaArchivoPlantillaTemporal($plantilla);

        $nombre_fichero = $this
            ->get('rm_plantilla_genera_plantilla_comunicacion')
            ->getRutaPlantilla($plantilla);


        // prepare BinaryFileResponse
        $response = new BinaryFileResponse($filePath);
        $response->trustXSendfileTypeHeader();
        $response->headers->set('Content-Type', 'text/html');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($nombre_fichero)
        );

        return $response;
    }

    public function uploadFicherosPlantillaAction($id_comunicacion, Request $request)
    {

        if (!$request->isMethod('POST')) {
            throw new BadRequestHttpException("No esta permitido el método utilizado para enviar la información.");
        }
        $em           = $this->getManager();
        $comunicacion = $em->getRepository('RMComunicacionBundle:Comunicacion')->find($id_comunicacion);
        $plantilla    = $comunicacion->getPlantilla();

        try {
            $file = $request->files->get('fichero');
            if (!$file instanceof UploadedFile) {
                throw new \Exception('No se ha subido ningun fichero');
            }

            $errores = $this
                ->get('rm_plantilla.upload_handler')
                ->handle($plantilla, $file);

        } catch (\Exception $e) {
            $this->addFlash('errores_plantilla', $e->getMessage());

            return $this->redirect($this->generateUrl('rm_comunicacion.comunicacion.editar_plantilla', [
                'idComunicacion' => $id_comunicacion
            ]));
        }

        if (count($errores) > 0) {
            $this->addFlash('errores_plantilla',
                sprintf('No se ha impotado la plantilla. Se han producido los siguientes errores: </br> %s ',
                    implode('</br>', $errores)));
        } else {
            $this->get('session')->getFlashBag()->add('mensaje', 'editar_ok');
        }

        return $this->redirect($this->generateUrl('rm_comunicacion.comunicacion.editar_plantilla', [
            'idComunicacion' => $id_comunicacion
        ]));
    }

    public function descargarInstruccionesPlantillaAction(Request $request)
    {
        $locale = $request->getLocale();
        $ruta   = $this->container->getParameter('ruta.instrucciones.maquetacion_plantilla');

        $instrucciones = $ruta . sprintf('%s_%s', $locale, 'instrucciones.txt');

        $response = new BinaryFileResponse($instrucciones);
        $response->headers->set('Content-Type', 'text/plain');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'instrucciones.txt'
        );

        return $response;
    }

}
