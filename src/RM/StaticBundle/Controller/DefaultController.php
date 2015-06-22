<?php

namespace RM\StaticBundle\Controller;

use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\Creatividad;
use RM\ComunicacionBundle\Form\CreatividadType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
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
        $em = $this->getDoctrine()->getManager($_SESSION['connection']);

        $comunicaciones = $em->getRepository('RMComunicacionBundle:Comunicacion')->findAll();

        $ids = array_map(
            function (Comunicacion $comunicacion) {
                return $comunicacion->getIdComunicacion();
            }, $comunicaciones);

        $plantillas = [];

        foreach ($ids as $id) {
            $plantillas[] = $em->getRepository('RMPlantillaBundle:Plantilla')->obtenerPlantillaByIdComunicacion($id);
        }

        return $this->render('RMStaticBundle:Default:plantillas.html.twig', ['plantillas' => $plantillas]);
    }

    public function plantillaAction($idPlantilla)
    {
        $em = $this->getDoctrine()->getManager($_SESSION['connection']);

        $plantilla = $em->getRepository('RMPlantillaBundle:Plantilla')->find($idPlantilla);

        $webpath = $this->container->getParameter('web_path');
        $cliente = $this->getUser()->getCliente();

        $rutaPlantilla = $webpath . '/' . $cliente . '/plantillas/' . $plantilla->getIdPlantilla() . '.html';

        if (!file_exists($rutaPlantilla)) {
            $this->get('rm_plantilla_genera_plantilla_comunicacion')
                ->creaArchivoPlantilla($plantilla, $cliente);
        }

        $error = $this->get('rm_plantilla_genera_plantilla_comunicacion')
            ->compruebaPlantilla($plantilla);

        $this->get('rm_plantilla.email_parser')
            ->setPlantilla($plantilla)
            ->parse($plantilla);

        if (empty($error)) {
            return REsponse::create(file_get_contents($rutaPlantilla), 200);
        }

        return Response::create(implode('', $error), 200);

    }


}
