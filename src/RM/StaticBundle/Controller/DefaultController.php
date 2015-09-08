<?php

namespace RM\StaticBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Campaign;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\Creatividad;
use RM\ComunicacionBundle\Form\CreatividadType;
use RM\ProductoBundle\Form\Type\CampaignType;
use RM\ProductoBundle\Form\Type\NumPromocionesType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends RMController
{
	public function indexAction($name)
    {
        $creatividad = new Creatividad();

        $form = $this->createForm(new CreatividadType(), $creatividad, [
            ]);

        return $this->render('RMStaticBundle:Default:index.html.twig', [
               'form' => $form->createView()
            ]);
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
        $ruta_batch = $this->getParameter('ruta.batch');
        $ruta_batch = $this->getUser()->getCliente() . '/' . $ruta_batch . $id . '.xml';

        if(!file_exists($ruta_batch)) {
            $this->addFlash('mensaje', 'mensaje.error.descargar.fichero');
            return $this->redirectToRoute('direct_monitor_controlador_fases', ['id_instancia' => $id]);
        }

        $response = new Response();
        $response->setContent(file_get_contents($ruta_batch));
        $response->headers->set('Content-type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', basename($ruta_batch)));

        return $response;
    }




}
