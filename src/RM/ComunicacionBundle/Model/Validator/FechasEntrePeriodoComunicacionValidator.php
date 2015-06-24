<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 25/02/2015
 * Time: 12:52
 */

namespace RM\ComunicacionBundle\Model\Validator;


use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\SegmentoComunicacion;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FechasEntrePeriodoComunicacionValidator extends ConstraintValidator
{
    /**
     * @param mixed                          $entity
     * @param FechasEntrePeriodoComunicacion $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        if (!$entity instanceof SegmentoComunicacion) {
            return;
        }


        $comunicacion = $entity->getIdComunicacion();
        if (!$comunicacion instanceof Comunicacion) {
            return;
        }

        /** @var bool $fechasEntrePeriodo */
        $fechasEntrePeriodo = (
            $entity->getFecInicio() >= $comunicacion->getFecInicio() &&
            $entity->getFecFin() <= $comunicacion->getFecFin()
        );

        if (false == $fechasEntrePeriodo) {
            $this->context->addViolation($constraint->message, [], null);
        }

        if (false == ($entity->getFecInicio() >= $comunicacion->getFecInicio())) {
            $this->context->addViolationAt('fecInicio', $constraint->errorFechaInicio, [], null);
        }

        if (false == ($entity->getFecFin() <= $comunicacion->getFecFin())) {
            $this->context->addViolationAt('fecFin', $constraint->errorFechaFin, [], null);
        }
    }
} 