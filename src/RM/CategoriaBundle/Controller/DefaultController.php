<?php

namespace RM\CategoriaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function obtenerFichaNivelesAction($idOpcionMenuSup, $idOpcionMenuIzq, $nivel)
    {
        $paginator = $this->get('knp_paginator');
    	$servicio     = $this->get("categoriaService");

    	$selectNiveles    = $servicio->getNivelesCategoria();
    	$selectCategorias = $servicio->getCategorias($nivel);

        $pagination = $paginator->paginate($selectCategorias, $this->get('request')->get('page',1), 15 );
    	
    	return $this->render('RMCategoriaBundle:Default:indexAvanzado.html.twig', [
    			'idOpcionMenuSup'  => $idOpcionMenuSup,
    			'idOpcionMenuIzq'  => $idOpcionMenuIzq,
    			'selectNiveles'    => $selectNiveles,
    			'selectCategorias' => $pagination,
    			'nivel'            => $nivel
    	]);

    }
    
    public function guardarCategoriasAvanzadasAction(Request $request)
    {	/*Parametros principales:
    	 	nivel: Nivel de Categoria donde se asociaran las categorias
    		guardarCat + idCategoria: Categorias seleccionadas parta ser asociadas
    	Acci�n:
    		Asocia o desasocia las categorias de un nivel seg�n los paramentros pasados si existen o no.
    	*/
    	$servicio = $this->get("categoriaService");
    	 
    	$servicio->guardarCategoriasAsocbyPost($request);
    	
    	$this->get('session')->getFlashBag()->add('mensaje','mensaje.ok.editar');
    	 
    	return $this->redirect($this->generateUrl('data_avanced_nivelCategoria', [
    			'nivel' => $request->get('nivel')]));
    }

}
