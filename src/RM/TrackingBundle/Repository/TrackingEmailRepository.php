<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 02/12/2015
 * Time: 11:29
 */

namespace RM\TrackingBundle\Repository;


use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\RMMongoBundle\DependencyInjection\MongoService;
use RM\TrackingBundle\Model\EventsCount;
use RM\TrackingBundle\Model\EventsHourCount;


class TrackingEmailRepository extends MongoService
{

    /**
     * Encuentra el número de clientes únicos que provocaron un evento
     * {$match: {instancia:3}},
     * {$group: {_id: {tipo: "$tipo", cliente: "$cliente"}}},
     * {$group: {_id: "$_id.tipo", total: {"$sum": 1}}}
     *
     * @param $instancia
     *
     * @return EventsCount
     */
    public function findOpenAndClickRate(InstanciaComunicacion $instancia)
    {
        $match = [
            '$match' => [
                'instancia' => $instancia->getIdInstancia()
            ]
        ];

        $group1 = [
            '$group' => [
                '_id' => [
                    'tipo'    => '$tipo',
                    'cliente' => '$cliente'
                ]
            ]
        ];

        $group2 = [
            '$group' => [
                '_id'   => '$_id.tipo',
                'total' => ['$sum' => 1]
            ]
        ];

        $res = $this
            ->getCollecion()
            ->aggregate($match, $group1, $group2);

        $res = isset($res['result']) ? $res['result'] : [];

        return EventsCount::createFromMongoArray($res);
    }

    /**
     * @return \MongoCollection
     */
    private function getCollecion()
    {
        return $this
            ->database
            ->selectCollection('tracking');

    }

    public function findEventsByInstancia($instancia)
    {
        $match = ['$match' => ['instancia' => $instancia]];

        $res = $this->getCollecion()->aggregate($match);


        return isset($res['result']) ? $res['result'] : null;
    }

    /**
     * Devuelve el número de eventos que se han producido a una hora especifica
     * clasificado por tipo de evento
     *
     * {$match: {instancia: 3}},
     * {$unwind: "$time_bucket"},
     * {$match: {time_bucket: {$regex: /-hour/i}}},
     * {$project: {time_bucket: {$substr: ["$time_bucket", 11, 2]}, cliente: 1}},
     * {$group: {'_id': {hora: "$time_bucket", cliente: "$cliente"}}},
     * {$group: {'_id': "$_id.hora", total: {$sum: 1}}}
     *
     * @param $instancia InstanciaComunicacion
     * @return EventsHourCount
     */
    public function getNumeroEventosPorHora(InstanciaComunicacion $instancia)
    {
        $match   = ['$match' => ['instancia' => $instancia->getIdInstancia()]];
        $unwind  = ['$unwind' => '$time_bucket'];
        $match2  = ['$match' => ['time_bucket' => ['$regex' => new \MongoRegex('/-hour/i')]]];
        $project = ['$project' => ['hora' => ['$substr' => ['$time_bucket', 11, 2]], 'cliente' => 1, 'tipo' => 1]];
        $group   = ['$group' => ['_id' => ['hora' => '$hora', 'tipo' => '$tipo'], 'total' => ['$sum' => 1]]];

        $res = $this->getCollecion()->aggregate($match, $unwind, $match2, $project, $group);

        $res = isset($res['result']) ? $res['result'] : [];

        return EventsHourCount::createFromMongoArray($res);
    }

} 