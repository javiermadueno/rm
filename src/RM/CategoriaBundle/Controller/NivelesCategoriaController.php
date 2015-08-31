<?php

namespace RM\CategoriaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use RM\CategoriaBundle\Form\NivelCategoriaCollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NivelesCategoriaController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->get('rm.manager')->getManager();

        $niveles = $em->getRepository('RMCategoriaBundle:NivelCategoria')->findAll();

        $form = $this->createForm(new NivelCategoriaCollectionType(), [
                'niveles' => $niveles
            ]);

        $niveles = new ArrayCollection($niveles);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();

            foreach($data['niveles']  as $nivel) {
                if (false === $niveles->contains($nivel)) {
                    $em->persist($nivel);
                }
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add('mensaje','mensaje.ok.editar');
        }

        return $this->render('RMCategoriaBundle:NivelesCategoria:index.html.twig', [
              'form' => $form->createView()
            ]);

    }

}
