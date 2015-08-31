<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 06/05/2015
 * Time: 11:25
 */

namespace RM\RMMongoBundle\DependencyInjection;


use RM\RMMongoBundle\Util;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * Class EstadisticasClientes
 *
 * @package RM\RMMongoBundle\DependencyInjection
 */
class EstadisticasClientes extends MongoService
{

    /**
     * @param TokenStorageInterface $security
     * @param array                 $config
     */
    public function __construct(TokenStorageInterface $security, $config)
    {
        parent::__construct($security, $config);
        $this->collection = $this->database->selectCollection('res_cliente');
    }

    /**
     * @param array $meses
     * @param array $segmentos
     *
     * @return mixed
     * @throws \Exception
     */
    public function findEstadisticasClientesByMesYSegmento($meses = [], array $segmentos)
    {
        $this->compruebaFechaMes($meses);

        $match = [
            '$match' => [
                "fecha" => ['$in'  => $meses],
                "ls"    => ['$all' => $segmentos]
            ]
        ];

        $group = [
            '$group' => [
                "_id"        => '$fecha',
                "num_cli"    => ['$sum' => 1],
                "compra_med" => ['$avg' => '$c'],
                "frec_med"   => ['$avg' => '$f'],
                "tick_med"   => ['$avg' => '$tm'],
                "a1_med"     => ['$avg' => '$a1'],
                "a2_med"     => ['$avg' => '$a2'],
                "a3_med"     => ['$avg' => '$a3'],
                "rec_med"    => ['$avg' => '$rec']
            ]
        ];

        $res = $this->collection->aggregate($match, $group);
        $res = $this->consolidaDatosMeses($meses, $res['result']);

        $resultado = array_reduce($res, function ($result, $elem) {
            $result[$elem['_id']] = [
                "num_cli"    => $elem['num_cli'],
                "compra_med" => $elem['compra_med'],
                "frec_med"   => $elem['frec_med'],
                "tick_med"   => $elem['tick_med'],
                "a1_med"     => $elem['a1_med'],
                "a2_med"     => $elem['a2_med'],
                "a3_med"     => $elem['a3_med'],
                "rec_med"    => $elem['rec_med']
            ];

            return $result;
        });


        return $resultado;
    }

    /**
     * @param array $meses
     *
     * @throws \Exception
     */
    private function compruebaFechaMes(array $meses)
    {
        foreach ($meses as $mes) {
            if (!preg_match('/^(19|20)\d\d([-])(0[1-9]|1[012])$/', $mes)) {
                throw new \Exception('El formato de la fecha no es válido');
            }
        }
    }

    /**
     * @param array $meses
     * @param array $resultado
     *
     * @return array
     */
    protected function consolidaDatosMeses($meses = [], $resultado = [])
    {
        $series = [];

        foreach ($meses as $mes) {
            if (false === $key = array_search($mes, array_column($resultado, '_id'))) {
                $total = [
                    '_id'        => $mes,
                    "num_cli"    => 0,
                    "compra_med" => 0,
                    "frec_med"   => 0,
                    "tick_med"   => 0,
                    "a1_med"     => 0,
                    "a2_med"     => 0,
                    "a3_med"     => 0,
                    "rec_med"    => 0
                ];
            } else {
                $total = $resultado[$key];
            }

            $series[] = $total;
        }

        return $series;
    }

    /**
     * @param array $meses
     * @param array $segmentos
     *
     * @return array
     * @throws \Exception
     */
    public function findNumeroClientosPorSegmentos($meses = [], $segmentos = [])
    {
        if (empty($meses)) {
            $meses = Util::getUltimosMeses(new \DateTime('-1 month'), 5);
        }

        $this->compruebaFechaMes($meses);

        sort($meses, SORT_STRING);
        sort($segmentos, SORT_NUMERIC);

        $res = $this->collection->aggregate(
            [
                '$match' => [
                    "fecha" => ['$in' => $meses],
                    'ls'    => ['$in' => $segmentos]
                ]
            ],
            [
                '$project' => [
                    'fecha' => 1,
                    "_id"   => 1,
                    "ls"    => 1
                ]
            ],
            [
                '$unwind' => '$ls'
            ],
            [
                '$match' => [
                    'ls' => ['$in' => $segmentos]
                ]
            ],
            [
                '$group' => [
                    "_id"  => ['fecha' => '$fecha', 'segmento' => '$ls'],
                    'data' => ['$sum'  => 1]
                ]
            ],
            [
                '$sort' => [
                    "_id.fecha"    => 1,
                    "_id.segmento" => 1
                ]
            ],
            [
                '$project' => [
                    'fecha'    => '$_id.fecha',
                    'segmento' => '$_id.segmento',
                    'total'    => '$data'
                ]
            ]
        );

        $resultado = $res['result'];
        $series    = $this->consolidaDatos($meses, $segmentos, $resultado);

        return $series;


    }

    /**
     * @param array $meses
     * @param array $segmentos
     * @param array $resultado
     *
     * @return array
     */
    protected function consolidaDatos($meses = [], $segmentos = [], $resultado = [])
    {
        $meses = array_unique($meses);

        $series = [];
        foreach ($meses as $mes) {
            foreach ($segmentos as $segmento) {
                $id = ['fecha' => $mes, 'segmento' => $segmento];

                if (false === $key = array_search($id, array_column($resultado, '_id'))) {
                    $total = 0;
                } else {
                    $total = $resultado[$key]['total'];
                }

                if (false === $index = array_search($mes, array_column($series, 'name'))) {
                    $series[] = [
                        'name' => $mes,
                        'data' => [$total]
                    ];
                } else {
                    $series[$index]['data'][] = $total;
                }
            }
        }

        return $series;
    }

    /**
     * @param $meses
     * @param $id_segmento_estado
     * @param $segmentos
     *
     * @return array
     * @throws \Exception
     */
    public function findNumeroClientesPorEstadoYPorSegmentos($meses, $id_segmento_estado, $segmentos)
    {
        $this->compruebaFechaMes($meses);

        sort($meses, SORT_STRING);
        sort($segmentos, SORT_NUMERIC);

        $res = $this->collection->aggregate(
            [
                '$match' => [
                    "fecha" => ['$in' => $meses],
                    '$and'  => [
                        ['ls' => ['$in' => $id_segmento_estado]],
                        ['ls' => ['$in' => $segmentos]]
                    ]
                ]
            ],
            [
                '$project' => [
                    'fecha' => 1,
                    "_id"   => 1,
                    "ls"    => 1
                ]
            ],
            [
                '$unwind' => '$ls'
            ],
            [
                '$match' => [
                    'ls' => ['$in' => $segmentos]
                ]
            ],
            [
                '$group' => [
                    "_id"  => ['fecha' => '$fecha', 'segmento' => '$ls'],
                    'data' => ['$sum'  => 1]
                ]
            ],
            [
                '$sort' => [
                    "_id.fecha"    => 1,
                    "_id.segmento" => 1
                ]
            ],
            [
                '$project' => [
                    'fecha'    => '$_id.fecha',
                    'segmento' => '$_id.segmento',
                    'total'    => '$data'
                ]
            ]
        );

        $resultado = $res['result'];
        $series    = $this->consolidaDatos($meses, $segmentos, $resultado);

        return $series;


    }

} 