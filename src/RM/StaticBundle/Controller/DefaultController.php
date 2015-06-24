<?php

namespace RM\StaticBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\Creatividad;
use RM\ComunicacionBundle\Form\CreatividadType;
use Symfony\Component\HttpFoundation\Response;


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




}
