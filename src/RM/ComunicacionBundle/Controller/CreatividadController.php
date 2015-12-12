<?php

namespace RM\ComunicacionBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use RM\AppBundle\Controller\RMController;
use RM\ComunicacionBundle\Entity\CampaingCreatividad;
use RM\ComunicacionBundle\Entity\Creatividad;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\ComunicacionBundle\Form\CreatividadType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CreatividadController extends RMController
{

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getManager();

        $nombre = $request->get('nombre', '');
        $creatividades = $em
            ->getRepository('RMComunicacionBundle:Creatividad')
            ->obtenerCreatividadByFiltroDQL($nombre);


        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage(5);

        $selectCreatividad = $paginator
            ->paginate($creatividades)
            ->getResult();

        if($request->isXmlHttpRequest()) {
            return $this->render('RMComunicacionBundle:Creatividad:listadoCreatividad.html.twig', [
                'objCreatividades'       => $selectCreatividad,
                'nombre'                 => $nombre
            ]);
        }

        return $this->render('RMComunicacionBundle:Creatividad:index.html.twig', [
            'objCreatividades'       => $selectCreatividad,
            'nombre'                 => $nombre
        ]);

    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function newAction(Request $request)
    {
        $em = $this->getManager();

        $pathGenerator = $this->get('rm.clientpathurlgenerator');


        $creatividad = new Creatividad();
        $creatividad->setEstado(1);

        $form = $this->createForm(new CreatividadType(), $creatividad, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('rm_comunicacion.creatividad.new')
        ]);

        $form->add('submit', 'submit', ['label' => 'boton.guardar']);

        $form->handleRequest($request);
        if($form->isValid()) {
            $em->persist($creatividad);
            $em->flush();

            $path = $pathGenerator->getRutaImagenesCreatividades($absolute = true);

            $creatividad->uploadImagen($path);
            $em->flush();

            return $this->redirectToRoute('rm_comunicacion.creatividad.index');
        }

        return $this->render('@RMComunicacion/Creatividad/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getManager();

        $pathGenerator = $this->get('rm.clientpathurlgenerator');

        $creatividad = $em->getRepository('RMComunicacionBundle:Creatividad')->find($id);

        if(!$creatividad instanceof Creatividad) {
            throw $this->createNotFoundException('No se ha encontrado la creatividad');
        }

        $form = $this->createForm(new CreatividadType(), $creatividad, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('rm_comunicacion.creatividad.edit', ['id' => $id])
        ]);

        $form->add('submit', 'submit', ['label' => 'boton.editar']);

        $form->handleRequest($request);

        if($form->isValid()) {

            $path = $pathGenerator->getRutaImagenesCreatividades($absolute = true);

            $creatividad->uploadImagen($path);
            $em->flush();

            return $this->redirectToRoute('rm_comunicacion.creatividad.index');
        }

        return $this->render('RMComunicacionBundle:Creatividad:edit.html.twig', [
            'form' => $form->createView(),
            'creatividad' => $creatividad
        ]);
    }


    public function searchCreatividadesPopoupAction($nombre = "")
    {
        $em = $this->getManager();
        $request    = $this->container->get('request');

        $extensionFormatoImages = [".jpeg", ".jpg", ".gif", ".tiff", ".bmp", ".png"];

        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage(5); // Para poner el numero de item que se quieren por pagina

        $creatividades = $em->getRepository('RMComunicacionBundle:Creatividad')->obtenerCreatividadByFiltroDQL($nombre);

        $selectCreatividad = $paginator->paginate($creatividades)->getResult();

        return $this->render('RMComunicacionBundle:Creatividad:buscadorCreatividadPopup.html.twig', [
            'id_id'                  => $request->get('id_id'),
            'id_nombre'              => $request->get('id_nombre'),
            'objCreatividades'       => $selectCreatividad,
            'extensionFormatoImages' => $extensionFormatoImages,
            'nombre'                 => $nombre
        ])
            ;
    }

    public function searchActualizarCreatividadesPopupAction(Request $request)
    {
        $em = $this->getManager();
        $nombre = $request->get('nombre', '');
        $extensionFormatoImages = [".jpeg", ".jpg", ".gif", ".tiff", ".bmp", ".png"];

        $paginator = $this->get('ideup.simple_paginator');
        $paginator->setItemsPerPage(5); // Para poner el numero de item que se quieren por pagina

        $creatividades = $em
            ->getRepository('RMComunicacionBundle:Creatividad')
            ->obtenerCreatividadByFiltroDQL($nombre);

        $selectCreatividad = $paginator
            ->paginate($creatividades)
            ->getResult();

        return $this->render('RMComunicacionBundle:Creatividad:buscadorCreatividadPopupListado.html.twig', [
            'objCreatividades'       => $selectCreatividad,
            'extensionFormatoImages' => $extensionFormatoImages,
            'nombre'                 => $nombre,
        ])
            ;
    }

}
