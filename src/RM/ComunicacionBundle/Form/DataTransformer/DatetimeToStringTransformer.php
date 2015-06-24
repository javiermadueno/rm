<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 26/02/2015
 * Time: 9:30
 */

namespace RM\ComunicacionBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DatetimeToStringTransformer implements DataTransformerInterface
{
    private $formato;

    public function __construct($formato)
    {
        $this->formato = $formato;
    }

    /**
     * @param mixed $date
     *
     * @return mixed|null
     */
    public function transform($date)
    {
        if (!$date) {
            return null;
        }

        return $date->format($this->formato);
    }

    /**
     * @param mixed $string
     *
     * @return \DateTime|mixed|null
     */
    public function reverseTransform($string)
    {
        if (empty($string) || !is_string($string)) {
            return null;
        }

        $date = \DateTime::createFromFormat($this->formato, $string);

        if (!$date) {
            throw new TransformationFailedException(sprintf(
                'No se ha podido crear la fecha a partir de "%s"',
                $string
            ));
        }

        return $date;
    }
} 