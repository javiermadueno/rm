<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 13/02/2015
 * Time: 12:15
 */

namespace RM\ComunicacionBundle\Model\Validator;
use RM\ComunicacionBundle\Model\Interfaces\FechaInicioFinInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class CompruebaFechasValidator extends ConstraintValidator
{
    public function validate($entidad, Constraint $constraint)
    {
        if(!$entidad instanceof FechaInicioFinInterface) {
            return;
        }

        $fechaInicio = $entidad->getFecInicio();
        $fechaFin = $entidad->getFecFin();

        if($fechaInicio > $fechaFin) {
            $this->context->addViolationAt('fecFin', $constraint->message, array(), null);
        }
    }
} 