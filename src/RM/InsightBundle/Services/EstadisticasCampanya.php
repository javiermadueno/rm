<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 23/10/2015
 * Time: 12:14
 */

namespace RM\InsightBundle\Services;


use DateTime;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\RMMongoBundle\DependencyInjection\MongoService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EstadisticasCampanya extends MongoService
{
    public function __construct(TokenStorageInterface $security, array $config)
    {
        parent::__construct($security, $config);
        $this->collection = $this->database->selectCollection('res_comunicacion');
    }


    public function getResumenCampanya(
        Comunicacion $comunicacion,
        DateTime $fechaInicio = null,
        Datetime $fechaFin = null
    ) {

        $rules = $this->getCondicionMatch($comunicacion, $fechaInicio, $fechaFin);

        $match = [
            '$match' => ['$and' => [$rules]]
        ];


        $group = [
            '$group' => [
                '_id'                => '$fec_fin',
                'numero_instancias'  => ['$sum' => 1],
                'clientes'           => ['$sum' => '$clientes'],
                'target'             => ['$sum' => '$target'],
                'slots'              => ['$sum' => '$slots'],
                'total_ventas'       => ['$sum' => '$total_ventas'],
                'numero_promociones' => ['$sum' => '$numero_promociones'],
                'impactos'           => ['$sum' => '$impactos'],
                'nombre'             => ['$first' => '$nombre']
            ]
        ];

        $project = [
            '$project' => [
                '_id'                => 1,
                'numero_instancias'  => 1,
                'clientes'           => 1,
                'target'             => 1,
                'slots'              => 1,
                'total_ventas'       => 1,
                'numero_promociones' => 1,
                'impactos'           => 1,
                'nombre'             => 1,
                'reactividad'        => [
                    '$cond' => [
                        ['$eq' => ['$target', 0]],
                        '$target',
                        ['$divide' => ['$clientes', '$target']]
                    ]
                ],
                'redencion'          => [
                    '$cond' => [
                        ['$eq' => ['$impactos', 0]],
                        '$impactos',
                        ['$divide' => ['$slots', '$impactos']]
                    ]
                ],
            ]
        ];

        $sort = [
            '$sort' => ['_id' => 1]
        ];

        $res = $this->collection->aggregate($match, $group, $project, $sort);

        return $res['result'];
    }


    public function getDistribucionClientesPorSegmentos(
        Comunicacion $comunicacion,
        DateTime $fechaInicio = null,
        DateTime $fechaFin = null,
        array $segmentos
    ) {
        if (empty($segmentos)) {
            //return null;
        }

        $rules = $this->getCondicionMatch($comunicacion, $fechaInicio, $fechaFin);

        $match = [
            '$match' => ['$and' => [$rules]]
        ];

        $unwind = [
            '$unwind' => '$info_target'
        ];

        $unwind2 = [
            '$unwind' => '$info_reactividad'
        ];

        $project = [
            '$project' => [
                'matches'          => ['$eq' => ['$info_target.id', '$info_reactividad.id']],
                'comunicacion'     => 1,
                'instancia'        => 1,
                'info_target'      => 1,
                'info_reactividad' => 1
            ]
        ];

        $match2 = [
            '$match' => [
                'matches' => true,
                //'info_target.id' => ['$in' => $segmentos]
            ]
        ];

        $group = [
            '$group' => [
                '_id'         => [
                    'nombre'   => '$info_target.nombre',
                    'segmento' => '$info_target.id'
                ],
                'target'      => ['$sum' => '$info_target.total'],
                'reactividad' => ['$sum' => '$info_reactividad.total'],
            ]
        ];

        $project2 = [
            '$project' => [
                '_id'         => false,
                'nombre'      => '$_id.nombre',
                'id'          => '$_id.segmento',
                'target'      => '$target',
                'reactividad' => '$reactividad'
            ]
        ];

        $res = $this->collection->aggregate($match, $unwind, $unwind2, $project, $match2, $group, $project2);


        return $res['result'];

    }

    private function getCondicionMatch(Comunicacion $comunicacion, DateTime $fechaInicio = null, DateTime $fechaFin = null)
    {
        $rules = ['comunicacion' => $comunicacion->getIdComunicacion()];

        if ($fechaInicio && $fechaFin) {
            $rules = array_merge($rules, [
                'fec_fin' => [
                    '$gte' => $fechaInicio->format('Y-m'),
                    '$lte' => $fechaFin->format('Y-m')
                ]
            ]);
        } elseif ($fechaInicio) {
            $rules = array_merge($rules, [
                'fec_fin' => [
                    '$gte' => $fechaInicio->format('Y-m')
                ]
            ]);
        } elseif ($fechaFin) {
            $rules = array_merge($rules, [
                'fec_fin' => [
                    '$lte' => $fechaFin->format('Y-m')
                ]
            ]);
        }

        return $rules;

    }

} 