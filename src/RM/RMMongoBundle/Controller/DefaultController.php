<?php

namespace RM\RMMongoBundle\Controller;

use RM\AppBundle\Controller\RMController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \NumberFormatter;

class DefaultController extends RMController
{

    /**
     * @param   $request    Request
     * @return              Response
     *
     * @Route("/poblacion", name="mongo_calcula_poblacion")
     */
    public function calculaPoblacionAction(Request $request)
    {
        $condicion  = $request->get('condicion');
        $fecha      = $request->get('fecha_busqueda');

        try{
            $service   = $this->get('rm.mongo.calcula_poblacion');
            $poblacion = $service->calculaPoblacion($condicion, $fecha);
        } catch(\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        return new Response($poblacion);
    }


    /**
     * @Route(name="rm_mongo.default.number", path="/number")
     * @param Request $request
     * @return Response
     */
    public function numberAction(Request $request)
    {

        $request->setLocale('es');
        $form = $this->createFormBuilder()
            ->add('numero', 'number', [
                'required' => true,
                'grouping' => true,
                'attr'     => ['lang' => $request->getLocale()]
            ])
            ->add('submit', 'submit')
        ->getForm();

        $form->handleRequest($request);
        $formatter = NumberFormatter::create($request->getLocale(), NumberFormatter::GROUPING_SEPARATOR_SYMBOL);
        $numero    = 0;

        if($form->isValid()) {
            $numero = $form->getData()['numero'];
            $form->addError(new FormError(
                sprintf('El número es correcto y es: %s', $formatter->parse($numero))
            ));
        }


        return $this->render('RMMongoBundle::formulario.html.twig', [
            'form'   => $form->createView(),
            'numero' => $formatter->format($numero)
        ]);
    }

}
