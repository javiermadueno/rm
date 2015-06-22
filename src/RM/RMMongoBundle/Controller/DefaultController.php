<?php

namespace RM\RMMongoBundle\Controller;

use RM\RMMongoBundle\Document\ClienteSegmento;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     * @Route("/poblacion", name="mongo_calcula_poblacion")
     */
    public function calculaPoblacionAction()
    {

        $request = $this->get('request');
        $condicion = $request->get('condicion');
        $fecha = $request->get('fecha_busqueda');

        try {
            $service = $this->get('rm.mongo.calcula_poblacion');
            $poblacion = $service->calculaPoblacion($condicion, $fecha);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        return new Response($poblacion);
    }


    /**
     * @Route("/hello/{name}", name="mongo_index" )
     * @Template()
     */
    public function indexAction($name)
    {
        $em = $this->get('doctrine_mongodb')->getManager();

        $repo = $em->getRepository('RMMongoBundle:ClienteSegmento');

        $time1 = -microtime(true);
        $clientes = $repo->findClientesEnSegmentos($in = [25, 26], $notIn = [10]);

        echo "Numero de clientes: ";

        $time1 += microtime(true);
        echo "Tiempo: " . number_format($time1, 3) . "</br>";


        $time2 = -microtime(true);
        echo "==============" . "</br>";
        echo "Segunda Consulta:" . "</br>";
        echo "==============" . "</br>";

        echo "Numero de clientes: ";
        $time2 += microtime(true);
        echo "Tiempo: " . number_format($time2, 3) . "</br>";

        return ['name' => $name];
    }

    /**
     * @Route("/crear/clientes")
     * @Template()
     */
    public function crearClientesAction()
    {
        $em = $this->get('doctrine_mongodb')->getManager();

        $segmentos1 = [];
        $segmentos2 = [];
        $segmentos3 = [];

        for ($i = 10; $i < 20; $i++) {
            $segmentos1[] = $i;
        }

        $segmentos2 = array_map(function ($elem) {
            return $elem + 5;
        }, $segmentos1);

        $segmentos3 = array_map(function ($elem) {
            return $elem + 8;
        }, $segmentos1);

        for ($i = 1; $i < 30000; $i++) {
            $cliente = new ClienteSegmento();
            $cliente->setSegmento(${'segmentos' . rand(1, 3)})
                ->setCliente('C' . $i);

            $em->persist($cliente);
        }

        $em->flush();
    }

    /**
     * @Route(path="/mongo/", name="prueba_mongo")
     */
    public function mongoAction()
    {
        $mongo = new \MongoClient('mongodb://192.168.100.92');

        $db = $mongo->selectDB('1');


        $condicion_raw = '{"$and": [{"segmento": {"$in": [26]}},{"$or": [{"segmento": {"$in": [27]}},{"segmento": {"$in": [28]}}]}]}';
        $condicion = json_decode($condicion_raw);

        function toArray($d)
        {
            if (is_object($d)) {
                $d = get_object_vars($d);
            }

            if (is_array($d)) {
                return array_map(__FUNCTION__, $d);
            } else {
                return $d;
            }
        }

        ;

        $condicion = toArray($condicion);

        $coleccion = 'cliente_segmento';
        $cliente_segmento = $db->$coleccion;

        $time1 = -microtime(true);
        $cursor = $cliente_segmento->count($condicion);
        $time1 += microtime(true);

        /*echo "Función Count()<br>";
        echo "--------------------<br>";
        echo "Tiempo: " . number_format($time1 ,3) ."<br>";

        var_dump($cursor);*/

        $pipeline_match = [
            '$match' => $condicion
        ];

        $pipeline_group = [
            '$group' => [
                "_id"   => null,
                "count" => ['$sum' => 1]
            ]
        ];

        $time2 = -microtime(true);
        $res1 = $cliente_segmento->aggregate($pipeline_match, $pipeline_group);
        $time2 += microtime(true);


        /*echo "Funcion Aggregate<br>";
        echo "--------------------<br>";
        echo "Tiempo: " . number_format($time2 ,3) ."<br>";

        var_dump($res1['result'][0]['count']);*/

        $comando = 'db.cliente_segmento.aggregate([{$match: %condicion% }, {$group: { _id: null, count: {$sum: 1}}}])';
        $comando = str_replace('%condicion%', $condicion_raw, $comando);


        $time3 = -microtime(true);
        $res = $db->execute($comando);
        $time3 += microtime(true);

        /*
        echo "Comando Literal<br>";
        echo "--------------------<br>";
        echo "Tiempo: " . number_format($time3, 3) . "<br>";
        var_dump($res['retval']['_firstBatch'][0]['count']);*/

        $mongo->close(true);

        return $this->render('@RMMongo/Default/resultadoConsultas.html.twig', [
            'resCount'        => $cursor,
            'resAggregate'    => $res1['result'][0]['count'],
            'resRaw'          => $res['retval']['_firstBatch'][0]['count'],
            'tiempoCount'     => number_format($time1, 3),
            'tiempoAggregate' => number_format($time2, 3),
            'tiempoRaw'       => number_format($time3, 3)
        ]);


    }

    /**
     * @Route(path="/clientes", name="rm_mongo_default_clientes")
     */
    public function clientesAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();

        $clientes = $dm->getRepository('RMMongoBundle:InstanciaComunicacionCliente')
            ->findBySlot(12);


        return $this->render('@RMMongo/blanco.html.twig', [
            'variable' => $clientes
        ]);
    }

    /**
     * @Route(name="rm_mongo_default_rellena_plantilla", path="/rellena/plantilla")
     * @param Request $request
     */
    public function rellenaPlantillaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager($_SESSION['connection']);

        $plantilla = $em->find('RMPlantillaBundle:Plantilla', 1);
        $cliente = $em->find('RMClienteBundle:Cliente', 1);

        $this->get('rm_plantilla.email_parser')->parse($plantilla, $cliente);
    }

    /**
     * @Route(name="rm_mongo_resultado_mensual", path="/resultado/mensual")
     */
    public function resultadoMensualAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();

        $clientes = $dm->getRepository('RMMongoBundle:ResultadoMensual')
            ->findMesesDisponibles();


        return $this->render('@RMMongo/blanco.html.twig', [
            'variable' => $clientes
        ]);
    }

    /**
     * @Route(name="rm_mongo_default_estadisticas", path="/estadisticas")
     */
    public function estadisticasAction()
    {
        $mes = "2012-01";
        $mes2 = "2012-02";

        $nombre_segmentos = $this->container->getParameter('segmentos');

        $segmentos = $this->get('rm.manager')->getManager()
            ->getRepository('RMSegmentoBundle:Segmento')
            ->findSegmentosByNombre($nombre_segmentos);

        $ids_segmentos = array_values($segmentos);

        $clientes = $this->get('rm.mongo.estadisticas_clientes')
            ->findEstadisticasClientesByMesYSegmento($mes, $mes2, $ids_segmentos);

        return $this->render('@RMMongo/blanco.html.twig', [
            'variable' => $clientes
        ]);
    }
}
