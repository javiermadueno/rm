<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 06/05/2015
 * Time: 9:34
 */

namespace RM\RMMongoBundle\DependencyInjection;


use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class CalculaPoblacionService extends MongoService
{

    public function __construct(TokenStorageInterface $security, $config)
    {
        parent::__construct($security, $config);
        $this->collection = $this->database->selectCollection('cliente_segmento');
    }

    /**
     * Calcula el número de clientes que existen en base de datos que cumplen la condición
     *
     * @param   string      $condicion
     * @param   string|null $fecha
     *
     * @return  int
     */
    public function calculaPoblacion($condicion, $fecha)
    {

        if (!$condicion) {
            throw new InvalidParameterException();
        }

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

        $condicion = json_decode($condicion);
        $condicion = toArray($condicion);

        $fecha = empty($fecha) || is_null($fecha) ? new \MongoDate() : new \MongoDate(strtotime($fecha));

        $condicion_fecha_inicio = [
            'fIni' => [
                '$lt' => $fecha
            ]
        ];

        $condicion_fecha_fin = [
            'fFin' => [
                '$gt' => $fecha
            ]
        ];

        $final = [
            '$and' => [
                $condicion,
                $condicion_fecha_inicio,
                $condicion_fecha_fin
            ]
        ];

        $pipeline_match = [
            '$match' => $final
        ];

        $pipeline_group = [
            '$group' => [
                "_id"   => null,
                "count" => ['$sum' => 1]
            ]
        ];

        $res = $this->collection->aggregate($pipeline_match, $pipeline_group);

        $poblacion = empty($res['result']) ? 0 : $res['result'][0]['count'];
        return intval($poblacion);

    }
}
