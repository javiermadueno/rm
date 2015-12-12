<?php

namespace RM\TrackingBundle\Controller;

use RM\AppBundle\Controller\RMController;
use RM\TrackingBundle\Document\TrackingEmail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;


/**
 * Class EmailTrackingController
 *
 * @Route(path="/tracking")
 */
class EmailTrackingController extends RMController
{
    /**
     * @Route(path="/aperture", name="email_tracking.incrementa_apertura")
     *
     * @param $request Request
     *
     * @return Response
     */
    public function apertureAction(Request $request)
    {

        $centro    = $request->query->get('centro');



        $dm = $this->get('doctrine_mongodb')->getManager($centro);

        $tracking = TrackingEmail::createApertureEventFromRequest($request);

        if($tracking instanceof TrackingEmail) {
            $dm->persist($tracking);
            $dm->flush();
        }

        return $this->imagenInvisible();
    }

    /**
     * @return BinaryFileResponse
     */
    private function imagenInvisible()
    {

        $imagen   = new File($this->getParameter('web_path') . '/images/bcarrow.png');
        $response = new BinaryFileResponse($imagen);
        $response->setMaxAge(60);

        return $response;
    }

    /**
     * @Route(path="/promocion", name="email_tracking.redirecciona_promocion")
     * @param Request $request
     *
     * @return Response
     */
    public function clickProductoAction(Request $request)
    {
        $centro    = $request->query->get('centro');
        $tracking = TrackingEmail::createClickEventFromRequest($request);

        if (is_null($centro)) {
            return new Response();
        }

        if($tracking instanceof TrackingEmail) {
            $dm = $this->get('doctrine_mongodb')->getManager($centro);
            $dm->persist($tracking);
            $dm->flush();
        }

        //Todo con el numero de promocion redireccionar a la URL correcta.
        //Todo se pueden tambien obtener estadisticas de cuantas veces se ha visitado la promocion


        return new Response();

    }

    /**
     * @Route(path="/generate", name="email_tracking.generate")
     */
    public function generateAction()
    {
        $centro = 3;

        $dm= $this->get('doctrine_mongodb')->getManager($centro);

        $response = new StreamedResponse(function() use ($dm) {
            $date = new \DateTime();

            for($i=0; $i<10000; $i++) {

                $date = new \DateTime();
                $date->setTime(rand(0,23), rand(0,59), rand(0,59));
                $date->setDate(2015,12,rand(0,30));

                $cliente = rand(1, 4996);
                $tracking = new TrackingEmail();
                $tracking
                    ->setCliente($cliente)
                    ->setInstancia(11)
                    ->setTipo(TrackingEmail::OPEN)
                    ->setFecha($date)
                    ->calculateTimeBucketing()
                ;

                $dm->persist($tracking);

                echo sprintf("%s: El cliente %s ha abierto el mail de la instancia %s</br>", $date->format('d-m-Y'), $cliente, 3);

                flush();
            }

            $dm->flush();
        });

        return $response;

    }

    /**
     * @Route(path="/click", name="email_tracking.click")
     * @return StreamedResponse
     */
    public function clickAction()
    {
        $centro = 3;

        $dm= $this->get('doctrine_mongodb')->getManager($centro);

        $response = new StreamedResponse(function() use ($dm) {
            $date = new \DateTime();

            for($i=0; $i<1000; $i++) {

                $date = new \DateTime();
                $date->setTime(rand(0,23), rand(0,59), rand(0,59));
                $date->setDate(2015,12,rand(0,30));

                $cliente = rand(1, 4996);
                $tracking = new TrackingEmail();
                $tracking
                    ->setCliente($cliente)
                    ->setInstancia(11)
                    ->setTipo(TrackingEmail::CLICK)
                    ->setFecha($date)
                    ->setPromocion(8)
                    ->calculateTimeBucketing()
                ;

                $dm->persist($tracking);

                echo sprintf("%s:\tEl cliente %s ha hecho click en la promocion %s\n", $date->format('d-m-Y'), $cliente, 8);

                flush();
            }

            $dm->flush();
        });

        return $response;
    }
}
