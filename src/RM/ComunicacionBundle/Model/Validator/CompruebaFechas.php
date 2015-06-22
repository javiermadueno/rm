<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 13/02/2015
 * Time: 11:22
 */

namespace RM\ComunicacionBundle\Model\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompruebaFechas extends Constraint
{
    public $message = 'La fecha de fin tiene que ser posterior a la fecha de inicio';


    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
} 