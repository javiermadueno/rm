<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 25/02/2015
 * Time: 12:47
 */

namespace RM\ComunicacionBundle\Model\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Comprueba que las fechas del segmento comunicacion esten comprendidas
 * entres las fechas de inicio y fin de la comunicacion
 *
 * @Annotation
 */
class FechasEntrePeriodoComunicacion extends Constraint
{
    public $message =
        'Las fechas de Inicio y Fin tienen que estar comprendidas en el periodo definido de la comunicación';

    public $errorFechaInicio =
        'La fecha de Inicio tiene que ser porterior a la fecha de inicio de la comunicacion';

    public $errorFechaFin =
        'La fecha de Fin tiene que ser anterior a la fecha de fin de la comunicación';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
} 