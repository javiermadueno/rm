<?php

namespace RM\StaticBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Campaign;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\Creatividad;
use RM\ComunicacionBundle\Form\CreatividadType;
use RM\PlantillaBundle\Entity\Plantilla;
use RM\ProductoBundle\Form\Type\CampaignType;
use RM\ProductoBundle\Form\Type\NumPromocionesType;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class DefaultController extends RMController
{
	public function indexAction()
    {
        $generator = $this->get('rm.clientpathurlgenerator');
        $rutas[] = $generator->getRutaPlantillas($absolute=true);
        $rutas[] = $generator->getRutaComunicacionesGeneradas($absolute=true);
        $rutas[] = $generator->getRutaImagenesCreatividades($absolute=true);
        $rutas[] = $generator->getRutaImagenesProducto($absolute=true);
        $rutas[] = $generator->getRutaBatch($absolute=true);
        $rutas[] = file_exists($generator->getRutaPlantillas($absolute=true));

        //return $this->render('RMStaticBundle:Default:index.html.twig');

        return Response::create($rutas);
	}

    public function plantillasAction()
    {
        $em = $this->getManager();

        $comunicaciones = $em->getRepository('RMComunicacionBundle:Comunicacion')->findAll();

        $ids = array_map(
            function(Comunicacion $comunicacion){
                return $comunicacion->getIdComunicacion();
            }, $comunicaciones);

        $plantillas = [];

        foreach($ids as $id) {
            $plantillas[] = $em->getRepository('RMPlantillaBundle:Plantilla')->obtenerPlantillaByIdComunicacion($id);
        }

        return $this->render('RMStaticBundle:Default:plantillas.html.twig', ['plantillas' => $plantillas]);
    }

    public function plantillaAction($idPlantilla)
    {
        $em = $this->getManager();

        $plantilla = $em->getRepository('RMPlantillaBundle:Plantilla')->find($idPlantilla);

        $webpath = $this->container->getParameter('web_path');
        $cliente = $this->getUser()->getCliente();

        $rutaPlantilla = $webpath .'/'. $cliente.'/plantillas/'.$plantilla->getIdPlantilla().'.html';

        if(!file_exists($rutaPlantilla)){
           $this->get('rm_plantilla_genera_plantilla_comunicacion')
               ->creaArchivoPlantilla($plantilla, $cliente);
        }

        $error  = $this->get('rm_plantilla_genera_plantilla_comunicacion')
            ->compruebaPlantilla($plantilla);

        $this->get('rm_plantilla.email_parser')
            ->setPlantilla($plantilla)
            ->parse($plantilla, $cliente);

        if(empty($error))
        {
            return REsponse::create(file_get_contents($rutaPlantilla), 200);
        }

        return Response::create(implode('', $error), 200);

    }

    public function campaignAction(Request $request)
    {
        $em = $this->getManager();

        $instancia = $em
            ->getRepository('RMComunicacionBundle:InstanciaComunicacion')
            ->find(4);

        $categorias = $this
            ->get('categoriaservice')
            ->getCatByInstancia(
                $instancia->getIdInstancia()
            );

        $instancia = new Campaign($instancia, new ArrayCollection($categorias));

        return $this->render('RMStaticBundle:Default:campaign.html.twig', [
            'campaign' => $instancia,
        ]);

    }

    public function numPromocionesAction(Request $request)
    {
        return $this->render('RMStaticBundle:Default:index.html.twig');
    }

    public function descargarBatchAction(Request $request, $id)
    {
        $ruta_batch = $this->get('rm.clientpathurlgenerator')->getRutaBatchFor($id . '.xml', true);

        if(!file_exists($ruta_batch)) {
            $this->addFlash('mensaje', 'mensaje.error.descargar.fichero');
            return $this->redirectToRoute('direct_monitor_controlador_fases', ['id_instancia' => $id]);
        }

        /*
        $response = new BinaryFileResponse($ruta_batch);
        $response->headers->set('Content-Type', 'text/xml');
        */


        $response = new Response();
        $response->setContent(file_get_contents($ruta_batch));
        $response->headers->set('Content-type', 'text/xml');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', basename($ruta_batch)));


        return $response;
    }

    public function getAbsoluteUrlAction()
    {
        $requestStack = $this->get('request_stack');

        $package = new PathPackage(
            '/',
            new StaticVersionStrategy('v1'),
            new RequestStackContext($requestStack)
        );

        $path = '3/imagenesProducto/157.jpg';
        $request = $requestStack->getMasterRequest();

        $prefix = $request->getPathInfo();
        $last = strlen($prefix) - 1;
        $pos = strrpos($prefix, '/');
        if ($last !== $pos ) {
            $prefix = substr($prefix, 0, $pos).'/';
        }

        $url = $request->getUriForPath($prefix.$path);

        $ulr = $this->get('twig.extension.httpfoundation')->generateAbsoluteUrl($path);

        return Response::create($url, Response::HTTP_OK);
    }

    public function uploadAction(Request $request)
    {
        $file = [];
        $errores = [];
        $plantilla = $this
            ->getManager()
            ->getRepository('RMPlantillaBundle:Plantilla')
            ->find(2);

        $form = $this->createFormBuilder($file)
            ->add('file', 'file', ['required' => true])
            ->add('submit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $file = $form->getData()['file'];

            $errores = $this->get('rm_plantilla.upload_handler')
                ->handle($plantilla, $file);
        }

        return $this->render('@RMStatic/Default/upload.html.twig', [
            'form'=>$form->createView(),
            'errores' => $errores
        ]);
    }
}
